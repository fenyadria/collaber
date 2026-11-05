<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotAdmin()) {
            return $redirect;
        }

        return view('admin.dashboard', [
            'statistics' => [
                ['label' => 'Total pengguna', 'value' => '1.284'],
                ['label' => 'Proyek pending', 'value' => '3'],
                ['label' => 'Laporan baru', 'value' => '2'],
                ['label' => 'Kategori', 'value' => '3'],
                ['label' => 'Skill', 'value' => '3'],
            ],
            'pendingProjects' => [
                ['name' => 'Sistem Informasi Perpustakaan', 'owner' => 'Dina Putri'],
                ['name' => 'Survei Pola Belajar Mahasiswa', 'owner' => 'Rafi Nugraha'],
                ['name' => 'Film Pendek Dokumenter', 'owner' => 'Nadia Putri'],
            ],
            'latestReports' => [
                ['title' => 'Anggota tidak bertanggung jawab', 'project' => 'Website Event Kampus'],
                ['title' => 'Owner proyek tidak aktif', 'project' => 'Aplikasi Donasi Buku'],
            ],
        ]);
    }

    public function verifikasiProyek(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotAdmin()) {
            return $redirect;
        }

        return view('admin.verifikasi-proyek', [
            'projects' => [
                ['name' => 'Sistem Informasi Perpustakaan', 'owner' => 'Dina Putri', 'category' => 'Aplikasi Web'],
                ['name' => 'Survei Pola Belajar Mahasiswa', 'owner' => 'Rafi Nugraha', 'category' => 'Penelitian'],
                ['name' => 'Film Pendek Dokumenter', 'owner' => 'Nadia Putri', 'category' => 'Multimedia'],
            ],
        ]);
    }

    public function kategoriSkill(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotAdmin()) {
            return $redirect;
        }

        return view('admin.kategori-skill', [
            'categories' => ['Aplikasi Web', 'Penelitian', 'Desain'],
            'skills' => ['Laravel', 'Figma', 'Statistik'],
        ]);
    }

    private function redirectIfNotAdmin(): ?RedirectResponse
    {
        if (session('user.role') !== 'admin') {
            return redirect('/login')->withErrors([
                'email' => 'Silakan login sebagai Admin terlebih dahulu.',
            ]);
        }

        return null;
    }
}
