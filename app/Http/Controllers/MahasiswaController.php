<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user()->load('profile');
        return view('mahasiswa.dashboard', compact('user'));
    }

    public function cariProyek(): View
    {
        $user = Auth::user()->load('profile');
        return view('mahasiswa.cari-proyek', [
            'user' => $user,
            'projects' => [
                ['name' => 'Redesain Aplikasi Event Kampus', 'quota' => 3, 'skills' => 'Figma, Riset UX.', 'type' => 'Cocok dengan skill-mu'],
                ['name' => 'Sistem Informasi Perpustakaan', 'quota' => 4, 'skills' => 'PHP, MySQL.', 'type' => 'Cocok dengan skill-mu'],
                ['name' => 'Survei Pola Belajar Mahasiswa', 'quota' => 5, 'skills' => 'Statistik, Penulisan.', 'type' => 'Penelitian'],
            ]
        ]);
    }

    public function lamaranSaya(): View
    {
        $user = Auth::user()->load('profile');
        return view('mahasiswa.lamaran-saya', [
            'user' => $user,
            'applications' => [
                ['project' => 'Aplikasi Kasir UMKM', 'owner' => 'Dina Putri', 'date' => '2026-11-10', 'status' => 'accepted'],
                ['project' => 'Redesain Aplikasi Event Kampus', 'owner' => 'Rafi Nugraha', 'date' => '2026-11-11', 'status' => 'pending'],
                ['project' => 'Film Pendek Dokumenter', 'owner' => 'Nadia Putri', 'date' => '2026-11-09', 'status' => 'rejected'],
            ]
        ]);
    }

    public function timSaya(): View
    {
        $user = Auth::user()->load('profile');
        return view('mahasiswa.tim-saya', compact('user'));
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
        ]);

        $user = Auth::user();

        $user->update([
            'name' => $validated['name'],
        ]);

        $profileData = [
            'nim' => $validated['nim'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
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
            ->with('success', 'Skill berhasil ditambahkan.');
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
            ->with('success', 'Level skill berhasil diperbarui.');
    }

    public function detachSkill(Skill $skill): RedirectResponse
    {
        Auth::user()->skills()->detach($skill->id);

        return redirect()
            ->route('mahasiswa.profil-skill')
            ->with('success', 'Skill berhasil dihapus dari profil.');
    }
}