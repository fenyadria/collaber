<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Skill;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MahasiswaController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user()->load(['profile', 'skills']);
        $userSkillIds = $user->skills->pluck('id');

        $applicationCounts = Application::query()
            ->where('user_id', $user->id)
            ->selectRaw("
                COUNT(*) FILTER (WHERE status = 'pending') AS pending_count,
                COUNT(*) FILTER (WHERE status = 'accepted') AS accepted_count,
                COUNT(*) FILTER (WHERE status = 'rejected') AS rejected_count
            ")
            ->first();

        $activeMembership = TeamMember::query()
            ->with(['project.tasks.assignees'])
            ->where('user_id', $user->id)
            ->whereHas('project', function ($q) {
                $q->where('status', '!=', 'completed');
            })
            ->latest('joined_at')
            ->first();

        $myTasks = $activeMembership?->project?->tasks ?? collect();

        $assignedTasks = $myTasks->filter(function ($task) use ($user) {
            return $task->assignees->contains('id', $user->id);
        });

        $dashboardTasks = ($assignedTasks->isNotEmpty() ? $assignedTasks : $myTasks)
            ->take(5);

        $hasSkills = $userSkillIds->isNotEmpty();

        $recommendedProjects = Project::query()
            ->with('skills')
            ->where('status', 'approved')
            ->when($hasSkills, function ($query) use ($userSkillIds) {
                $query->whereHas('skills', function ($q) use ($userSkillIds) {
                    $q->whereIn('skills.id', $userSkillIds);
                });
            })
            ->latest()
            ->take(3)
            ->get();

        return view('mahasiswa.dashboard', [
            'user' => $user,
            'userSkillIds' => $userSkillIds,
            'hasSkills' => $hasSkills,
            'pendingCount' => (int) ($applicationCounts->pending_count ?? 0),
            'acceptedCount' => (int) ($applicationCounts->accepted_count ?? 0),
            'rejectedCount' => (int) ($applicationCounts->rejected_count ?? 0),
            'dashboardTasks' => $dashboardTasks,
            'recommendedProjects' => $recommendedProjects,
        ]);
    }

    public function cariProyek(Request $request): View
    {
        $user = Auth::user()->load(['skills', 'profile']);
        $hasCv = filled($user->profile?->cv_path);
        $userSkillIds = $user->skills->pluck('id');

        $categories = Category::orderBy('name')->get();
        $allSkills = Skill::orderBy('name')->get();

        $hasActiveProject = TeamMember::query()
            ->where('user_id', $user->id)
            ->whereHas('project', function ($q) {
                $q->where('status', '!=', 'completed');
            })
            ->exists();

        $projects = Project::query()
            ->with(['category', 'skills'])
            ->withCount('teamMembers')
            ->where('status', 'approved')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'ilike', "%{$search}%")
                        ->orWhere('description', 'ilike', "%{$search}%")
                        ->orWhere('goal', 'ilike', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', $request->input('category_id'));
            })
            ->when($request->filled('skill_id'), function ($q) use ($request) {
                $skillId = $request->input('skill_id');
                $q->whereHas('skills', function ($sub) use ($skillId) {
                    $sub->where('skills.id', $skillId);
                });
            })
            ->when($request->boolean('available_only'), function ($q) {
                $q->whereRaw('max_members > (SELECT COUNT(*) FROM team_members WHERE team_members.project_id = projects.id)');
            })
            ->latest()
            ->get();

        return view('mahasiswa.cari-proyek', [
            'user' => $user,
            'projects' => $projects,
            'categories' => $categories,
            'allSkills' => $allSkills,
            'userSkillIds' => $userSkillIds,
            'hasActiveProject' => $hasActiveProject,
            'hasCv' => $hasCv,
        ]);
    }

    public function applyProyek(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'motivation' => ['required', 'string', 'max:1000'],
        ]);

        $user = Auth::user()->load('profile');
        $userId = $user->id;

        if ($project->status !== 'approved') {
            return back()->with('error', 'Proyek ini belum dibuka untuk pelamar.');
        }

        if (blank($user->profile?->cv_path)) {
            return back()->with('error', 'Kamu harus mengunggah CV di halaman Profil & Skill terlebih dahulu sebelum melamar proyek.');
        }

        $hasActiveProject = TeamMember::query()
            ->where('user_id', $userId)
            ->whereHas('project', function ($q) {
                $q->where('status', '!=', 'completed');
            })
            ->exists();

        if ($hasActiveProject) {
            return back()->with('error', 'Kamu masih tergabung dalam proyek aktif. Selesaikan proyekmu terlebih dahulu sebelum melamar proyek lain.');
        }

        $alreadyApplied = Application::query()
            ->where('user_id', $userId)
            ->where('project_id', $project->id)
            ->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'Kamu sudah pernah melamar ke proyek ini.');
        }

        $currentMembersCount = $project->teamMembers()->count();
        if (($project->max_members ?? 0) <= $currentMembersCount) {
            return back()->with('error', 'Kuota anggota untuk proyek ini sudah penuh.');
        }

        Application::create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'message' => $request->input('motivation'),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Lamaran berhasil dikirim!');
    }

    public function lamaranSaya(Request $request): View
    {
        $status = $request->input('status');
        $allowedStatuses = ['pending', 'accepted', 'rejected'];

        $applications = Application::query()
            ->with(['project.owner'])
            ->where('user_id', Auth::id())
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->get();

        return view('mahasiswa.lamaran-saya', [
            'applications' => $applications,
            'currentStatus' => in_array($status, $allowedStatuses, true) ? $status : null,
        ]);
    }

    public function timSaya(): View
    {
        $userId = Auth::id();

        $membership = TeamMember::query()
            ->with([
                'project.owner',
                'project.tasks.assignees',
                'project.teamMembers.user',
            ])
            ->where('user_id', $userId)
            ->orderByRaw("CASE WHEN EXISTS (SELECT 1 FROM projects WHERE projects.id = team_members.project_id AND projects.status != 'completed') THEN 0 ELSE 1 END")
            ->latest('joined_at')
            ->first();

        $project = $membership?->project;
        $tasks = $project?->tasks ?? collect();
        $teamMembers = $project?->teamMembers ?? collect();

        return view('mahasiswa.tim-saya', [
            'project' => $project,
            'teamMembers' => $teamMembers,
            'todoTasks' => $tasks->where('status', 'todo'),
            'inProgressTasks' => $tasks->whereIn('status', ['in_progress', 'in progress']),
            'doneTasks' => $tasks->where('status', 'done'),
        ]);
    }

    public function updateTaskStatus(Request $request, ProjectTask $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:todo,in_progress,done'],
        ]);

        $isMember = TeamMember::query()
            ->where('project_id', $task->project_id)
            ->where('user_id', Auth::id())
            ->exists();

        if (! $isMember) {
            abort(403, 'Kamu bukan anggota tim pada proyek ini.');
        }

        $task->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function uploadTaskSubmission(Request $request, ProjectTask $task): RedirectResponse
    {
        $validated = $request->validate([
            'submission' => ['required', 'file', 'mimes:pdf,zip,png,jpg,jpeg,webp,doc,docx', 'max:10240'],
            'submission_note' => ['nullable', 'string', 'max:255'],
        ]);

        $isMember = TeamMember::query()
            ->where('project_id', $task->project_id)
            ->where('user_id', Auth::id())
            ->exists();

        if (! $isMember) {
            abort(403, 'Kamu bukan anggota tim pada proyek ini.');
        }

        if ($task->submission_path) {
            Storage::disk('public')->delete($task->submission_path);
        }

        $path = $request->file('submission')->store('task-submissions', 'public');

        $task->update([
            'submission_path' => $path,
            'submission_note' => $validated['submission_note'] ?? null,
        ]);

        return back()->with('success', 'Hasil kerja berhasil diunggah.');
    }

    public function profilSkill(): View
    {
        $user = Auth::user()->load(['profile', 'skills']);
        $allSkills = Skill::orderBy('name')->get();

        return view('mahasiswa.profil-skill', [
            'user' => $user,
            'allSkills' => $allSkills,
        ]);
    }

    public function updateProfil(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:25'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $user = Auth::user()->load('profile');

        $user->update([
            'name' => $validated['name'],
        ]);

        $profileData = [
            'nim' => $validated['nim'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->profile?->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('cv')) {
            if ($user->profile?->cv_path) {
                Storage::disk('public')->delete($user->profile->cv_path);
            }
            $profileData['cv_path'] = $request->file('cv')->store('cvs', 'public');
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()
            ->route('mahasiswa.profil-skill')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function storeSkill(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'skill_id' => ['required', 'integer', 'exists:skills,id'],
            'level' => ['required', 'in:Pemula,Menengah,Mahir'],
        ]);

        Auth::user()->skills()->syncWithoutDetaching([
            $validated['skill_id'] => ['level' => $validated['level']],
        ]);

        return redirect()
            ->route('mahasiswa.profil-skill')
            ->with('success', 'Skill berhasil ditambahkan.')
            ->with('keep_editing', true);
    }

    public function updateSkill(Request $request, Skill $skill): RedirectResponse
    {
        $validated = $request->validate([
            'level' => ['required', 'in:Pemula,Menengah,Mahir'],
        ]);

        Auth::user()->skills()->updateExistingPivot($skill->id, [
            'level' => $validated['level'],
        ]);

        return redirect()
            ->route('mahasiswa.profil-skill')
            ->with('success', 'Level skill berhasil diperbarui.')
            ->with('keep_editing', true);
    }

    public function detachSkill(Skill $skill): RedirectResponse
    {
        Auth::user()->skills()->detach($skill->id);

        return redirect()
            ->route('mahasiswa.profil-skill')
            ->with('success', 'Skill berhasil dihapus dari profil.')
            ->with('keep_editing', true);
    }

    public function lihatCv(): StreamedResponse
    {
        $user = Auth::user()->load('profile');
        $cvPath = $user->profile?->cv_path;

        if (blank($cvPath) || ! Storage::disk('public')->exists($cvPath)) {
            abort(404, 'File CV tidak ditemukan.');
        }

        return Storage::disk('public')->response(
            $cvPath,
            'CV-' . str($user->name)->slug() . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}