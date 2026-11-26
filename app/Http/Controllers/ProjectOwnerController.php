<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Review;
use App\Models\Skill;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectOwnerController extends Controller
{
    private const STATUSES = ['draft', 'pending', 'approved', 'rejected'];

    /** Status proyek yang masih boleh diedit oleh owner. */
    private const DEFAULT_CATEGORIES = ['Aplikasi Web', 'Penelitian', 'Desain', 'Multimedia'];

    private const EDITABLE_STATUSES = ['draft', 'pending', 'rejected'];

    public function dashboard(): View
    {
        $ownerId = Auth::id();

        $projects = Project::query()->where('owner_id', $ownerId)->latest()->get();

        $applications = Application::query()
            ->whereHas('project', fn ($q) => $q->where('owner_id', $ownerId));

        return view('project-owner.dashboard', [
            'user' => Auth::user(),
            'statistics' => [
                ['label' => 'Total proyek', 'value' => $projects->count()],
                ['label' => 'Lamaran masuk', 'value' => (clone $applications)->count()],
                ['label' => 'Menunggu verifikasi', 'value' => $projects->where('status', 'pending')->count()],
                ['label' => 'Proyek berjalan', 'value' => $projects->where('status', 'approved')->count()],
            ],
            'projects' => $projects->take(5),
            'applicants' => (clone $applications)
                ->with(['user', 'project'])
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    public function proyekSaya(Request $request): View
    {
        $filter = in_array($request->query('status'), self::STATUSES, true)
            ? $request->query('status')
            : null;

        $projects = Project::query()
            ->with('category')
            ->withCount(['applications', 'teamMembers'])
            ->where('owner_id', Auth::id())
            ->when($filter, fn ($q) => $q->where('status', $filter))
            ->latest()
            ->get();

        return view('project-owner.proyek-saya', [
            'user' => Auth::user(),
            'projects' => $projects,
            'filter' => $filter,
            'statuses' => self::STATUSES,
        ]);
    }

    public function postingProyek(): View
    {
        return view('project-owner.posting-proyek', $this->formData(null));
    }

    public function simpanProyek(Request $request): RedirectResponse
    {
        $data = $this->validateProject($request);

        $project = Project::create([
            'owner_id' => Auth::id(),
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'goal' => $data['goal'] ?? null,
            'max_members' => $data['max_members'],
            'deadline' => $data['deadline'] ?? null,
            'status' => $data['action'] === 'submit' ? 'pending' : 'draft',
        ]);

        $this->syncSkills($project, $data['skills'] ?? []);

        return redirect()->route('project-owner.proyek-saya')->with('success', $data['action'] === 'submit'
            ? 'Proyek dikirim untuk verifikasi (status: pending).'
            : 'Draf proyek disimpan.');
    }

    public function editProyek(Project $project): View|RedirectResponse
    {
        $this->authorizeOwner($project);

        if (! in_array($project->status, self::EDITABLE_STATUSES, true)) {
            return redirect()->route('project-owner.proyek-saya')
                ->with('error', 'Proyek yang sudah disetujui tidak dapat diedit.');
        }

        return view('project-owner.posting-proyek', $this->formData($project));
    }

    public function updateProyek(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeOwner($project);

        if (! in_array($project->status, self::EDITABLE_STATUSES, true)) {
            return redirect()->route('project-owner.proyek-saya')
                ->with('error', 'Proyek yang sudah disetujui tidak dapat diedit.');
        }

        $data = $this->validateProject($request);

        // Draf pada proyek yang ditolak tetap berstatus rejected sampai dikirim ulang.
        $status = $data['action'] === 'submit'
            ? 'pending'
            : ($project->status === 'rejected' ? 'rejected' : 'draft');

        $project->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'goal' => $data['goal'] ?? null,
            'max_members' => $data['max_members'],
            'deadline' => $data['deadline'] ?? null,
            'status' => $status,
        ]);

        $this->syncSkills($project, $data['skills'] ?? []);

        return redirect()->route('project-owner.proyek-saya')->with('success', $data['action'] === 'submit'
            ? 'Proyek dikirim ulang untuk verifikasi (status: pending).'
            : 'Perubahan proyek disimpan.');
    }

    public function hapusProyek(Project $project): RedirectResponse
    {
        $this->authorizeOwner($project);

        if (! in_array($project->status, self::EDITABLE_STATUSES, true)) {
            return back()->with('error', 'Proyek yang sudah disetujui tidak dapat dihapus.');
        }

        $project->delete();

        return redirect()->route('project-owner.proyek-saya')->with('success', 'Proyek dihapus.');
    }

    public function pelamar(Request $request): View
    {
        $ownerId = Auth::id();

        $status = in_array($request->query('status'), ['pending', 'accepted', 'rejected'], true)
            ? $request->query('status')
            : null;

        $projectOptions = Project::query()
            ->where('owner_id', $ownerId)
            ->where('status', 'approved')
            ->orderBy('name')
            ->get(['id', 'name']);

        $projectId = $projectOptions->contains('id', (int) $request->query('project'))
            ? (int) $request->query('project')
            : null;

        $applications = Application::query()
            ->with(['user.profile', 'user.skills', 'project' => fn ($q) => $q->withCount('teamMembers')])
            ->whereHas('project', fn ($q) => $q->where('owner_id', $ownerId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->get();

        return view('project-owner.pelamar', [
            'user' => Auth::user(),
            'applications' => $applications,
            'projectOptions' => $projectOptions,
            'projectId' => $projectId,
            'status' => $status,
            'ratings' => $this->ratingsFor($applications->pluck('user_id')),
        ]);
    }

    public function updatePelamarStatus(Request $request, Application $application): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['accepted', 'rejected'])],
        ]);

        $project = $application->project;
        $this->authorizeOwner($project);

        $error = DB::transaction(function () use ($validated, $application, $project) {
            // Kunci baris proyek agar kuota tidak terlampaui saat accept bersamaan.
            Project::query()->whereKey($project->id)->lockForUpdate()->first();
            $application->refresh();

            if ($application->status !== 'pending') {
                return 'Lamaran ini sudah diproses.';
            }

            if ($validated['status'] === 'accepted') {
                if ($project->status !== 'approved') {
                    return 'Hanya proyek yang sudah disetujui yang dapat menerima anggota.';
                }

                if ($project->teamMembers()->count() >= $project->max_members) {
                    return 'Kuota anggota proyek ini sudah penuh.';
                }

                $inOtherProject = TeamMember::query()
                    ->where('user_id', $application->user_id)
                    ->whereHas('project', fn ($q) => $q->where('status', '!=', 'completed'))
                    ->exists();

                if ($inOtherProject) {
                    return 'Mahasiswa ini sudah tergabung di proyek aktif lain.';
                }

                TeamMember::create([
                    'project_id' => $project->id,
                    'user_id' => $application->user_id,
                    'joined_at' => now(),
                ]);
            }

            $application->update([
                'status' => $validated['status'],
                'responded_at' => now(),
            ]);

            return null;
        });

        if ($error) {
            return back()->with('error', $error);
        }

        return back()->with('success', $validated['status'] === 'accepted'
            ? 'Pelamar diterima dan ditambahkan ke tim.'
            : 'Pelamar ditolak.');
    }

    public function workspace(Request $request, ?Project $project = null): View
    {
        $ownerId = Auth::id();

        $projects = Project::query()
            ->where('owner_id', $ownerId)
            ->where('status', 'approved')
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($project) {
            $this->authorizeOwner($project);
            abort_unless($project->status === 'approved', 404);
        } else {
            $project = $projects->first();
        }

        if ($project) {
            $project = Project::query()
                ->with(['teamMembers.user', 'tasks.assignees'])
                ->findOrFail($project->id);
        }

        $tasks = $project?->tasks ?? collect();

        return view('project-owner.workspace', [
            'user' => Auth::user(),
            'projects' => $projects,
            'project' => $project,
            'teamMembers' => $project?->teamMembers ?? collect(),
            'ratings' => $this->ratingsFor(($project?->teamMembers ?? collect())->pluck('user_id')),
            'columns' => [
                'todo' => ['label' => 'To do', 'tasks' => $tasks->where('status', 'todo')],
                'in_progress' => ['label' => 'Dikerjakan', 'tasks' => $tasks->whereIn('status', ['in_progress', 'in progress'])],
                'done' => ['label' => 'Selesai', 'tasks' => $tasks->where('status', 'done')],
            ],
        ]);
    }

    public function simpanTask(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeOwner($project);
        abort_unless($project->status === 'approved', 404);

        $memberIds = $project->teamMembers()->pluck('user_id')->all();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'assignees' => ['nullable', 'array'],
            'assignees.*' => ['integer', Rule::in($memberIds)],
        ], [
            'title.required' => 'Judul tugas wajib diisi.',
            'deadline.after_or_equal' => 'Tenggat tugas tidak boleh sebelum hari ini.',
            'assignees.*.in' => 'Tugas hanya dapat diberikan kepada anggota tim.',
        ]);

        $task = $project->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'status' => 'todo',
        ]);

        $task->assignees()->sync($validated['assignees'] ?? []);

        return redirect()->route('project-owner.workspace', $project)->with('success', 'Tugas ditambahkan.');
    }

    public function updateTask(Request $request, ProjectTask $task): RedirectResponse
    {
        $project = $task->project;
        $this->authorizeOwner($project);

        $memberIds = $project->teamMembers()->pluck('user_id')->all();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'deadline' => ['nullable', 'date'],
            'assignees' => ['nullable', 'array'],
            'assignees.*' => ['integer', Rule::in($memberIds)],
        ], [
            'title.required' => 'Judul tugas wajib diisi.',
            'assignees.*.in' => 'Tugas hanya dapat diberikan kepada anggota tim.',
        ]);

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
        ]);

        $task->assignees()->sync($validated['assignees'] ?? []);

        return redirect()->route('project-owner.workspace', $project)->with('success', 'Tugas diperbarui.');
    }

    public function updateTaskStatus(Request $request, ProjectTask $task): RedirectResponse
    {
        $this->authorizeOwner($task->project);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['todo', 'in_progress', 'done'])],
        ]);

        $task->update(['status' => $validated['status']]);

        return redirect()->route('project-owner.workspace', $task->project_id)->with('success', 'Status tugas diperbarui.');
    }

    /**
     * Owner juga ikut mengerjakan: bisa mengunggah atau mengganti hasil kerja sebuah tugas.
     */
    public function uploadTaskSubmission(Request $request, ProjectTask $task): RedirectResponse
    {
        $this->authorizeOwner($task->project);

        $validated = $request->validate([
            'submission' => ['required', 'file', 'mimes:pdf,zip,png,jpg,jpeg,webp,doc,docx', 'max:10240'],
            'submission_note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($task->submission_path) {
            Storage::disk('public')->delete($task->submission_path);
        }

        $task->update([
            'submission_path' => $request->file('submission')->store('task-submissions', 'public'),
            'submission_note' => $validated['submission_note'] ?? null,
        ]);

        return redirect()->route('project-owner.workspace', $task->project_id)->with('success', 'Hasil kerja diunggah.');
    }

    public function hapusTask(ProjectTask $task): RedirectResponse
    {
        $this->authorizeOwner($task->project);

        $projectId = $task->project_id;

        if ($task->submission_path) {
            Storage::disk('public')->delete($task->submission_path);
        }

        $task->delete();

        return redirect()->route('project-owner.workspace', $projectId)->with('success', 'Tugas dihapus.');
    }

    /**
     * Rating rata-rata dan jumlah ulasan per pengguna, dikunci dengan id pengguna.
     */
    private function ratingsFor(iterable $userIds): Collection
    {
        return Review::query()
            ->whereIn('reviewee_id', collect($userIds)->unique()->values()->all())
            ->selectRaw('reviewee_id, AVG(rating) as avg, COUNT(*) as n')
            ->groupBy('reviewee_id')
            ->get()
            ->keyBy('reviewee_id');
    }

    private function authorizeOwner(Project $project): void
    {
        abort_unless($project->owner_id === Auth::id(), 403, 'Ini bukan proyek milikmu.');
    }

    private function formData(?Project $project): array
    {
        return [
            'user' => Auth::user(),
            'project' => $project?->load('skills'),
            'categories' => $this->categories(),
            'skills' => Skill::orderBy('name')->pluck('name'),
        ];
    }

    /**
     * Daftar kategori dari database. Jika admin belum menambahkan kategori,
     * kategori bawaan dibuat otomatis supaya form posting tetap bisa dipakai.
     */
    private function categories()
    {
        if (Category::query()->doesntExist()) {
            foreach (self::DEFAULT_CATEGORIES as $name) {
                Category::firstOrCreate(['name' => $name]);
            }
        }

        return Category::orderBy('name')->get();
    }

    private function validateProject(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'goal' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'max_members' => 'required|integer|min:1|max:50',
            'deadline' => 'nullable|date|after:today',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:50',
            'action' => 'required|in:draft,submit',
        ], [
            'name.required' => 'Judul proyek wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'category_id.required' => 'Pilih kategori proyek.',
            'category_id.exists' => 'Kategori tidak valid.',
            'max_members.required' => 'Kuota anggota wajib diisi.',
            'deadline.after' => 'Tenggat harus setelah hari ini.',
        ]);
    }

    private function syncSkills(Project $project, array $names): void
    {
        $ids = collect($names)
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique()
            ->map(fn ($name) => Skill::firstOrCreate(['name' => $name])->id)
            ->all();

        $project->skills()->sync($ids);
    }
}
