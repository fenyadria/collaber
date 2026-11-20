<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'user' => Auth::user(),
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

    public function verifikasiProyek(): View
    {
        return view('admin.verifikasi-proyek', [
            'user' => Auth::user(),
            'projects' => [
                ['name' => 'Sistem Informasi Perpustakaan', 'owner' => 'Dina Putri', 'category' => 'Aplikasi Web'],
                ['name' => 'Survei Pola Belajar Mahasiswa', 'owner' => 'Rafi Nugraha', 'category' => 'Penelitian'],
                ['name' => 'Film Pendek Dokumenter', 'owner' => 'Nadia Putri', 'category' => 'Multimedia'],
            ],
        ]);
    }

    public function kategoriSkill(): View
    {
        return view('admin.kategori-skill', [
            'user' => Auth::user(),
            'categories' => ['Aplikasi Web', 'Penelitian', 'Desain'],
            'skills' => ['Laravel', 'Figma', 'Statistik'],
        ]);
    }
}