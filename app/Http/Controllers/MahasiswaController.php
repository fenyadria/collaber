<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function dashboard(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotMahasiswa()) {
            return $redirect;
        }

        $user = session('user');
        return view('mahasiswa.dashboard', compact('user'));
    }

    public function cariProyek(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotMahasiswa()) {
            return $redirect;
        }

        $user = session('user');
        return view('mahasiswa.cari-proyek', [
            'user' => $user,
            'projects' => [
                ['name' => 'Redesain Aplikasi Event Kampus', 'quota' => 3, 'skills' => 'Figma, Riset UX.', 'type' => 'Cocok dengan skill-mu'],
                ['name' => 'Sistem Informasi Perpustakaan', 'quota' => 4, 'skills' => 'PHP, MySQL.', 'type' => 'Cocok dengan skill-mu'],
                ['name' => 'Survei Pola Belajar Mahasiswa', 'quota' => 5, 'skills' => 'Statistik, Penulisan.', 'type' => 'Penelitian'],
            ]
        ]);
    }

    public function lamaranSaya(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotMahasiswa()) {
            return $redirect;
        }

        $user = session('user');
        return view('mahasiswa.lamaran-saya', [
            'user' => $user,
            'applications' => [
                ['project' => 'Aplikasi Kasir UMKM', 'owner' => 'Dina Putri', 'date' => '2026-11-10', 'status' => 'accepted'],
                ['project' => 'Redesain Aplikasi Event Kampus', 'owner' => 'Rafi Nugraha', 'date' => '2026-11-11', 'status' => 'pending'],
                ['project' => 'Film Pendek Dokumenter', 'owner' => 'Nadia Putri', 'date' => '2026-11-09', 'status' => 'rejected'],
            ]
        ]);
    }

    public function timSaya(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotMahasiswa()) {
            return $redirect;
        }

        $user = session('user');
        return view('mahasiswa.tim-saya', compact('user'));
    }

    public function profilSkill(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNotMahasiswa()) {
            return $redirect;
        }

        $user = session('user');
        return view('mahasiswa.profil-skill', compact('user'));
    }

    private function redirectIfNotMahasiswa(): ?RedirectResponse
    {
        if (!session()->has('user')) {
            return redirect('/login')->withErrors([
                'email' => 'Silakan login terlebih dahulu.',
            ]);
        }

        return null;
    }
}