@extends('mahasiswa.layout')

@section('title', 'Dashboard Mahasiswa')

@section('content')
    @php
        $firstName = explode(' ', trim($user->name ?? 'Mahasiswa'))[0];
    @endphp

    <div class="mb-6">
        <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Halo, {{ $firstName }}</h2>
        <p class="text-sm text-slate-500 mt-1">Ringkasan aktivitasmu di Collaber.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-xs font-medium text-slate-500 mb-3">Lamaran pending</p>
            <p class="text-3xl font-bold text-slate-900">{{ $pendingCount }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-xs font-medium text-slate-500 mb-3">Diterima</p>
            <p class="text-3xl font-bold text-emerald-600">{{ $acceptedCount }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-xs font-medium text-slate-500 mb-3">Ditolak</p>
            <p class="text-3xl font-bold text-rose-600">{{ $rejectedCount }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Tugas Saya --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-base font-bold text-slate-900 mb-5">Tugas saya</h3>

            @if ($dashboardTasks->isEmpty())
                <p class="text-sm text-slate-400 py-6">Belum ada tugas aktif saat ini.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($dashboardTasks as $task)
                        @php
                            $statusKey = $task->status ?? 'todo';
                            [$statusLabel, $badgeClass] = match ($statusKey) {
                                'in_progress', 'in progress' => ['In Progress', 'bg-indigo-50 text-indigo-700'],
                                'done' => ['Done', 'bg-emerald-50 text-emerald-700'],
                                default => ['To Do', 'bg-slate-100 text-slate-600'],
                            };
                        @endphp

                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                            <p class="text-sm text-slate-700">{{ $task->title }}</p>
                            <span class="shrink-0 px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 mb-5">Rekomendasi sesuai skill</h3>

                @if ($recommendedProjects->isEmpty())
                    <p class="text-sm text-slate-400 py-4">
                        Belum ada rekomendasi proyek yang cocok dengan skill-mu.
                    </p>
                @else
                    @if (!$hasSkills)
                        <p class="text-xs text-slate-400 mb-3">
                            Belum ada skill di profil—menampilkan proyek terbaru.
                            <a href="{{ route('mahasiswa.profil-skill') }}" class="text-indigo-600 font-medium hover:underline">
                                Lengkapi skill
                            </a>
                        </p>
                    @endif
                    
                    <div class="divide-y divide-slate-100">
                        @foreach ($recommendedProjects as $project)
                            @php
                                $matchedSkills = $project->skills
                                    ->whereIn('id', $userSkillIds)
                                    ->pluck('name');
                                $skillText = $matchedSkills->isNotEmpty()
                                    ? $matchedSkills->implode(', ')
                                    : $project->skills->pluck('name')->implode(', ');
                            @endphp

                            <div class="py-3.5 first:pt-0">
                                <p class="text-sm font-bold text-slate-900">{{ $project->name }}</p>
                                @if ($skillText !== '')
                                    <p class="text-xs text-slate-400 mt-1">Cocok: {{ $skillText }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <a href="{{ route('mahasiswa.cari-proyek') }}"
                    class="block w-full text-center py-2.5 px-4 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
                    Lihat semua proyek
                </a>
            </div>
        </div>
    </div>
@endsection