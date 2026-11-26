@extends('project-owner.layout')

@section('title', 'Workspace')

@section('content')
    <header class="mb-5">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Workspace</h1>
        <p class="mt-1 text-sm text-slate-500">Kelola tim dan tugas untuk proyek yang sudah disetujui.</p>
    </header>

    @include('project-owner._flash')

    @if ($projects->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white px-5 py-10 text-center text-sm text-slate-500 shadow-sm">
            Belum ada proyek yang disetujui admin. Workspace aktif setelah proyekmu berstatus approved.
        </div>
    @else
        <nav aria-label="Pilih proyek" class="mb-4 flex flex-wrap gap-2">
            @foreach ($projects as $option)
                <a href="{{ route('project-owner.workspace', $option) }}" @class(['rounded-full px-3 py-1 text-xs font-semibold', 'bg-indigo-600 text-white' => $option->id === $project->id, 'bg-indigo-50 text-indigo-800' => $option->id !== $project->id])>{{ $option->name }}@if ($option->status === 'completed') (selesai)@endif</a>
            @endforeach
        </nav>

        @if ($project->status === 'completed')
            <div class="mb-4 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800" role="status">
                Proyek ini sudah <strong>selesai</strong>. Tugas dikunci, tapi kamu dan anggota tim masih bisa saling memberi ulasan atau melapor.
            </div>
        @endif

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <div class="space-y-5">
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="mb-4 font-semibold text-slate-900">Papan tugas: {{ $project->name }}</h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        @foreach ($columns as $column)
                            <div>
                                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $column['label'] }} ({{ $column['tasks']->count() }})</h3>
                                <ul class="space-y-2">
                                    @forelse ($column['tasks'] as $task)
                                        @include('project-owner._task-card', ['task' => $task, 'teamMembers' => $teamMembers, 'readonly' => $project->status === 'completed'])
                                    @empty
                                        <li class="text-xs text-slate-400">Kosong</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </section>

                @if ($project->status === 'approved')
                <form method="POST" action="{{ route('project-owner.workspace.tasks.store', $project) }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    @csrf
                    <h2 class="font-semibold text-slate-900">Tambah tugas</h2>

                    @if ($errors->any())
                        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                            <ul class="list-inside list-disc">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="title" class="mb-1 block text-sm font-semibold">Judul tugas</label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="task-description" class="mb-1 block text-sm font-semibold">Deskripsi</label>
                        <textarea id="task-description" name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label for="task-deadline" class="mb-1 block text-sm font-semibold">Tenggat</label>
                        <input id="task-deadline" name="deadline" type="date" value="{{ old('deadline') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:w-60">
                    </div>
                    <fieldset>
                        <legend class="mb-2 text-sm font-semibold">Tugaskan ke</legend>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($teamMembers as $member)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="assignees[]" value="{{ $member->user_id }}" class="peer sr-only" @checked(in_array($member->user_id, old('assignees', [])))>
                                    <span class="inline-block rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800 peer-checked:bg-indigo-600 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-400">{{ $member->user->name }}</span>
                                </label>
                            @empty
                                <span class="text-xs text-slate-500">Belum ada anggota. Terima pelamar dulu di menu Pelamar.</span>
                            @endforelse
                        </div>
                    </fieldset>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">Simpan tugas</button>
                </form>
                @endif
            </div>

            <aside class="h-fit rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-1 font-semibold text-slate-900">Anggota tim</h2>
                <p class="mb-3 text-xs text-slate-500">{{ $teamMembers->count() }} dari {{ $project->max_members }} anggota</p>
                <ul class="divide-y divide-slate-100">
                    @forelse ($teamMembers as $member)
                        <li class="flex flex-wrap items-center justify-between gap-x-2 py-2">
                            <span class="text-sm text-slate-800">{{ $member->user->name }}</span>
                            <span>@include('project-owner._rating', ['rating' => $ratings[$member->user_id] ?? null])</span>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-slate-500">Belum ada anggota.</li>
                    @endforelse
                </ul>
                @if ($project->status === 'approved')
                    <form method="POST" action="{{ route('project-owner.workspace.complete', $project) }}" onsubmit="return confirm('Tandai proyek ini selesai? Lamaran yang masih pending akan ditolak otomatis, dan tugas tidak bisa diubah lagi.')" class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                        @csrf
                        <p class="mb-2 text-xs text-slate-600">Sudah beres? Tandai selesai supaya anggota bisa bergabung ke proyek lain dan semua bisa saling mengulas.</p>
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Tandai proyek selesai</button>
                    </form>
                @endif

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('project-owner.pelamar', ['project' => $project->id]) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Lihat pelamar</a>
                    <button type="button" onclick="document.getElementById('dlg-laporan').showModal()" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Laporkan</button>
                    <button type="button" onclick="document.getElementById('dlg-ulasan').showModal()" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700">Beri ulasan</button>
                </div>
            </aside>
        </div>

        @include('partials.team-feedback', ['project' => $project, 'others' => $teamMembers->pluck('user')->filter()->values()])
    @endif
@endsection
