<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectOwnerController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected'];

    private const CATEGORIES = ['Aplikasi Web', 'Penelitian', 'Desain', 'Multimedia'];

    public function dashboard(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotOwner()) {
            return $redirect;
        }

        return view('project-owner.dashboard', [
            'statistics' => [
                ['label' => 'Total proyek', 'value' => '5'],
                ['label' => 'Lamaran masuk', 'value' => '12'],
                ['label' => 'Menunggu verifikasi', 'value' => '1'],
                ['label' => 'Proyek berjalan', 'value' => '2'],
            ],
            'projects' => $this->projects(),
            'applicants' => [
                ['name' => '[Nama Mahasiswa]', 'project' => 'Aplikasi Kasir UMKM'],
                ['name' => '[Nama Mahasiswa]', 'project' => 'Aplikasi Kasir UMKM'],
            ],
        ]);
    }

    public function proyekSaya(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotOwner()) {
            return $redirect;
        }

        $filter = in_array($request->query('status'), self::STATUSES, true)
            ? $request->query('status')
            : null;

        $projects = collect($this->projects())
            ->when($filter, fn ($items) => $items->where('status', $filter))
            ->values()
            ->all();

        return view('project-owner.proyek-saya', [
            'projects' => $projects,
            'filter' => $filter,
            'statuses' => self::STATUSES,
        ]);
    }

    public function postingProyek(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotOwner()) {
            return $redirect;
        }

        return view('project-owner.posting-proyek', [
            'categories' => self::CATEGORIES,
            'skills' => ['Laravel', 'MySQL', 'Figma'],
        ]);
    }

    public function simpanProyek(Request $request): RedirectResponse
    {
        if ($redirect = $this->redirectIfNotOwner()) {
            return $redirect;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:'.implode(',', self::CATEGORIES),
            'max_members' => 'required|integer|min:1|max:50',
            'deadline' => 'nullable|date|after:today',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:50',
            'action' => 'required|in:draft,submit',
        ], [
            'name.required' => 'Judul proyek wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'category.required' => 'Pilih kategori proyek.',
            'category.in' => 'Kategori tidak valid.',
            'max_members.required' => 'Kuota anggota wajib diisi.',
            'deadline.after' => 'Tenggat harus setelah hari ini.',
        ]);

        // TODO: simpan ke tabel projects. Saat ini login masih memakai akun hardcode,
        // belum ada baris user di database untuk dijadikan owner_id.
        $message = $request->input('action') === 'submit'
            ? 'Proyek dikirim untuk verifikasi (status: pending).'
            : 'Draf proyek disimpan.';

        return redirect()->route('project-owner.proyek-saya')->with('success', $message);
    }

    private function projects(): array
    {
        return [
            [
                'name' => 'Aplikasi Kasir UMKM',
                'category' => 'Aplikasi Web',
                'filled' => 3, 'quota' => 4,
                'applicants' => 5,
                'status' => 'approved',
                'reason' => null,
            ],
            [
                'name' => 'Sistem Informasi Perpustakaan',
                'category' => 'Aplikasi Web',
                'filled' => 0, 'quota' => 4,
                'applicants' => 0,
                'status' => 'pending',
                'reason' => null,
            ],
            [
                'name' => 'Poster Kampanye',
                'category' => 'Desain',
                'filled' => 0, 'quota' => 3,
                'applicants' => 0,
                'status' => 'rejected',
                'reason' => '[Catatan dari Admin]. Revisi proyek lalu kirim ulang untuk verifikasi.',
            ],
        ];
    }

    private function redirectIfNotOwner(): ?RedirectResponse
    {
        if (session('user.role') !== 'project_owner') {
            return redirect('/login')->withErrors([
                'email' => 'Silakan login sebagai Project Owner terlebih dahulu.',
            ]);
        }

        return null;
    }
}
