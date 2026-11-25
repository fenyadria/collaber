<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const USER_STATUSES = ['active', 'inactive'];

    private const USER_ROLES = ['admin', 'mahasiswa', 'project_owner'];

    private const REPORT_STATUSES = ['pending', 'in_review', 'resolved', 'dismissed'];

    private const REPORT_STATUS_LABELS = [
        'pending' => 'Baru',
        'in_review' => 'Sedang ditinjau',
        'resolved' => 'Selesai',
        'dismissed' => 'Ditolak',
    ];

    private const CATALOGS = [
        'kategori' => ['table' => 'categories', 'label' => 'Kategori'],
        'skill' => ['table' => 'skills', 'label' => 'Skill'],
    ];

    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'user' => Auth::user(),
            'statistics' => [
                ['label' => 'Total pengguna', 'value' => User::count()],
                ['label' => 'Proyek pending', 'value' => DB::table('projects')->where('status', 'pending')->count()],
                ['label' => 'Laporan baru', 'value' => DB::table('reports')->where('status', 'pending')->count()],
                ['label' => 'Kategori', 'value' => DB::table('categories')->count()],
                ['label' => 'Skill', 'value' => DB::table('skills')->count()],
            ],
            'pendingProjects' => DB::table('projects')
                ->join('users', 'users.id', '=', 'projects.owner_id')
                ->where('projects.status', 'pending')
                ->select('projects.name', 'users.name as owner_name')
                ->orderBy('projects.created_at')
                ->limit(5)
                ->get(),
            'latestReports' => DB::table('reports')
                ->join('users', 'users.id', '=', 'reports.user_id')
                ->select('reports.content', 'reports.status', 'users.name as reporter_name')
                ->latest('reports.created_at')
                ->limit(5)
                ->get(),
        ]);
    }

    public function pengguna(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = in_array($request->query('status'), self::USER_STATUSES, true)
            ? $request->query('status')
            : null;
        $role = in_array($request->query('role'), self::USER_ROLES, true)
            ? $request->query('role')
            : null;

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($role, fn ($query) => $query->where('role', $role))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengguna', [
            'users' => $users,
            'search' => $search,
            'filterStatus' => $status,
            'filterRole' => $role,
        ]);
    }

    public function detailPengguna(User $user): View
    {
        return view('admin.pengguna-detail', ['managedUser' => $user]);
    }

    public function updatePenggunaStatus(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::USER_STATUSES)],
        ]);

        if ($user->is(Auth::user()) && $validated['status'] !== 'active') {
            return back()->withErrors([
                'status' => 'Akun yang sedang digunakan tidak dapat dinonaktifkan.',
            ]);
        }

        $user->update(['status' => $validated['status']]);

        $message = $validated['status'] === 'active'
            ? 'Akun pengguna berhasil diaktifkan.'
            : 'Akun pengguna berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    public function laporan(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = in_array($request->query('status'), self::REPORT_STATUSES, true)
            ? $request->query('status')
            : null;

        $reports = DB::table('reports')
            ->join('users', 'users.id', '=', 'reports.user_id')
            ->select(
                'reports.id',
                'reports.content',
                'reports.status',
                'reports.created_at',
                'users.name as reporter_name',
                'users.email as reporter_email',
            )
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('reports.content', 'like', "%{$search}%")
                        ->orWhere('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%");
                });
            })
            ->when($status, fn (Builder $query) => $query->where('reports.status', $status))
            ->orderByDesc('reports.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.laporan', [
            'reports' => $reports,
            'search' => $search,
            'filterStatus' => $status,
            'statusLabels' => self::REPORT_STATUS_LABELS,
        ]);
    }

    public function updateLaporanStatus(Request $request, int $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::REPORT_STATUSES)],
        ]);

        abort_unless(DB::table('reports')->where('id', $report)->exists(), 404);

        DB::table('reports')
            ->where('id', $report)
            ->update([
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Tindak lanjut laporan berhasil disimpan.');
    }

    public function verifikasiProyek(): View
    {
        return view('admin.verifikasi-proyek', [
            'projects' => DB::table('projects')
                ->join('users', 'users.id', '=', 'projects.owner_id')
                ->join('categories', 'categories.id', '=', 'projects.category_id')
                ->where('projects.status', 'pending')
                ->select(
                    'projects.id',
                    'projects.name',
                    'users.name as owner_name',
                    'categories.name as category_name',
                )
                ->orderBy('projects.created_at')
                ->paginate(10),
        ]);
    }

    public function updateProyekStatus(Request $request, int $project): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        abort_unless(DB::table('projects')->where('id', $project)->exists(), 404);

        $updated = DB::table('projects')
            ->where('id', $project)
            ->where('status', 'pending')
            ->update([
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

        if (! $updated) {
            return back()->withErrors([
                'project' => 'Proyek ini sudah tidak menunggu verifikasi.',
            ]);
        }

        $message = $validated['status'] === 'approved'
            ? 'Proyek berhasil disetujui.'
            : 'Proyek berhasil ditolak.';

        return back()->with('success', $message);
    }

    public function kategoriSkill(): View
    {
        return view('admin.kategori-skill', [
            'categories' => DB::table('categories')->orderBy('name')->get(),
            'skills' => DB::table('skills')->orderBy('name')->get(),
        ]);
    }

    public function storeCatalog(Request $request, string $type): RedirectResponse
    {
        $catalog = $this->catalogDefinition($type);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique($catalog['table'], 'name')],
        ]);

        DB::table($catalog['table'])->insert([
            'name' => $validated['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.kategori-skill')->with('success', "{$catalog['label']} berhasil ditambahkan.");
    }

    public function updateCatalog(Request $request, string $type, int $item): RedirectResponse
    {
        $catalog = $this->catalogDefinition($type);
        abort_unless(DB::table($catalog['table'])->where('id', $item)->exists(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique($catalog['table'], 'name')->ignore($item)],
        ]);

        DB::table($catalog['table'])
            ->where('id', $item)
            ->update([
                'name' => $validated['name'],
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.kategori-skill')->with('success', "{$catalog['label']} berhasil diperbarui.");
    }

    public function deleteCatalog(string $type, int $item): RedirectResponse
    {
        $catalog = $this->catalogDefinition($type);
        abort_unless(DB::table($catalog['table'])->where('id', $item)->exists(), 404);

        $isInUse = match ($type) {
            'kategori' => DB::table('projects')->where('category_id', $item)->exists(),
            'skill' => DB::table('user_skills')->where('skill_id', $item)->exists()
                || DB::table('project_skills')->where('skill_id', $item)->exists(),
        };

        if ($isInUse) {
            return back()->withErrors([
                'delete' => "{$catalog['label']} sedang digunakan dan tidak dapat dihapus.",
            ]);
        }

        DB::table($catalog['table'])->where('id', $item)->delete();

        return redirect()->route('admin.kategori-skill')->with('success', "{$catalog['label']} berhasil dihapus.");
    }

    private function catalogDefinition(string $type): array
    {
        abort_unless(isset(self::CATALOGS[$type]), 404);

        return self::CATALOGS[$type];
    }
}
