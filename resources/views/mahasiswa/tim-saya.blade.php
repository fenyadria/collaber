@extends('mahasiswa.layout')

@section('title', 'Tim Saya')

@section('content')
    <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-6">Tim Saya</h2>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if (!$project)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Belum ada tim aktif</h3>
            <p class="text-sm text-slate-500 mb-6">
                Kamu belum tergabung dalam proyek mana pun. Cari proyek yang sesuai dengan skill-mu dan kirim lamaran.
            </p>
            <a href="{{ route('mahasiswa.cari-proyek') }}"
                class="inline-block py-2.5 px-6 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
                Cari Proyek
            </a>
        </div>
    @else
        @php
            $totalAnggota = $teamMembers->count() + ($project->owner ? 1 : 0);
        @endphp

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900">{{ $project->name }}</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Owner: {{ $project->owner?->name ?? '-' }}. Anggota: {{ $totalAnggota }} orang.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button"
                    class="py-2 px-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-xl text-xs transition">
                    Laporkan
                </button>
                <button type="button"
                    class="py-2 px-4 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
                    Beri ulasan
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-bold text-slate-900">To Do</h4>
                    <span class="text-xs font-semibold text-slate-400">{{ $todoTasks->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse ($todoTasks as $task)
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5">
                            <p class="text-xs font-medium text-slate-800">{{ $task->title }}</p>
                            @if ($task->deadline)
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Deadline: {{ $task->deadline->translatedFormat('d M Y') }}
                                </p>
                            @endif
                            @if ($task->assignees->isNotEmpty())
                                <p class="text-[11px] text-indigo-600 mt-1">
                                    PIC: {{ $task->assignees->pluck('name')->implode(', ') }}
                                </p>
                            @endif

                            <form action="{{ route('mahasiswa.tim-saya.tasks.status', $task) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" aria-label="Ubah status tugas"
                                    class="w-full text-[11px] font-medium bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <option value="todo" @selected($task->status === 'todo')>Pindah ke: To Do</option>
                                    <option value="in_progress" @selected($task->status === 'in_progress')>Pindah ke: In Progress</option>
                                    <option value="done" @selected($task->status === 'done')>Pindah ke: Done</option>
                                </select>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada tugas</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-bold text-slate-900">In Progress</h4>
                    <span class="text-xs font-semibold text-slate-400">{{ $inProgressTasks->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse ($inProgressTasks as $task)
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5">
                            <p class="text-xs font-medium text-slate-800">{{ $task->title }}</p>
                            @if ($task->deadline)
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Deadline: {{ $task->deadline->translatedFormat('d M Y') }}
                                </p>
                            @endif
                            @if ($task->assignees->isNotEmpty())
                                <p class="text-[11px] text-indigo-600 mt-1">
                                    PIC: {{ $task->assignees->pluck('name')->implode(', ') }}
                                </p>
                            @endif

                            <form action="{{ route('mahasiswa.tim-saya.tasks.status', $task) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" aria-label="Ubah status tugas"
                                    class="w-full text-[11px] font-medium bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <option value="todo" @selected($task->status === 'todo')>Pindah ke: To Do</option>
                                    <option value="in_progress" @selected($task->status === 'in_progress')>Pindah ke: In Progress</option>
                                    <option value="done" @selected($task->status === 'done')>Pindah ke: Done</option>
                                </select>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada tugas</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-bold text-slate-900">Done</h4>
                    <span class="text-xs font-semibold text-slate-400">{{ $doneTasks->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse ($doneTasks as $task)
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5">
                            <p class="text-xs font-medium text-slate-800">{{ $task->title }}</p>
                            @if ($task->deadline)
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Deadline: {{ $task->deadline->translatedFormat('d M Y') }}
                                </p>
                            @endif
                            @if ($task->assignees->isNotEmpty())
                                <p class="text-[11px] text-indigo-600 mt-1">
                                    PIC: {{ $task->assignees->pluck('name')->implode(', ') }}
                                </p>
                            @endif

                            <form action="{{ route('mahasiswa.tim-saya.tasks.status', $task) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" aria-label="Ubah status tugas"
                                    class="w-full text-[11px] font-medium bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <option value="todo" @selected($task->status === 'todo')>Pindah ke: To Do</option>
                                    <option value="in_progress" @selected($task->status === 'in_progress')>Pindah ke: In Progress</option>
                                    <option value="done" @selected($task->status === 'done')>Pindah ke: Done</option>
                                </select>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada tugas</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-sm font-bold text-slate-900 mb-4">Anggota tim</h4>
            <div class="flex items-center gap-2 flex-wrap">
                @if ($project->owner)
                    <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-semibold text-xs rounded-full">
                        {{ $project->owner->name }} (Owner)
                    </span>
                @endif

                @foreach ($teamMembers as $member)
                    @if ($member->user)
                        <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-semibold text-xs rounded-full">
                            {{ $member->user_id === Auth::id() ? 'Saya' : $member->user->name }}
                            @if ($member->position)
                                - {{ $member->position }}
                            @endif
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
@endsection