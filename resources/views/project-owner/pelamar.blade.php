@extends('project-owner.layout')

@section('title', 'Pelamar')

@section('content')
    <header class="mb-5">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Pelamar</h1>
        <p class="mt-1 text-sm text-slate-500">Tinjau profil dan skill pemohon, lalu terima atau tolak.</p>
    </header>

    @include('project-owner._flash')

    <form method="GET" action="{{ route('project-owner.pelamar') }}" class="mb-4 flex flex-wrap items-center gap-2">
        <select name="project" aria-label="Filter proyek" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="">Semua proyek</option>
            @foreach ($projectOptions as $option)
                <option value="{{ $option->id }}" @selected($projectId === $option->id)>{{ $option->name }}</option>
            @endforeach
        </select>
        <select name="status" aria-label="Filter status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="">Semua status</option>
            @foreach (['pending', 'accepted', 'rejected'] as $option)
                <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">Terapkan</button>
    </form>

    <div class="space-y-4">
        @forelse ($applications as $application)
            @php
                $applicant = $application->user;
                $full = $application->project->team_members_count >= $application->project->max_members;
            @endphp
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-slate-900">{{ $applicant->name }}</h2>
                        <p class="mt-1 text-xs text-slate-500">
                            Melamar: <span class="font-medium text-slate-700">{{ $application->project->name }}</span>
                            &middot; {{ $application->created_at->diffForHumans() }}
                            @if ($applicant->profile?->nim) &middot; NIM {{ $applicant->profile->nim }} @endif
                        </p>
                    </div>
                    @include('project-owner._status-badge', ['status' => $application->status])
                </div>
                <p class="mt-1">@include('project-owner._rating', ['rating' => $ratings[$applicant->id] ?? null])</p>

                @if ($application->message)
                    <p class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700">&ldquo;{{ $application->message }}&rdquo;</p>
                @endif

                @if ($applicant->profile?->bio)
                    <p class="mt-3 text-sm text-slate-600">{{ $applicant->profile->bio }}</p>
                @endif

                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse ($applicant->skills as $skill)
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800">{{ $skill->name }} &middot; {{ $skill->pivot->level }}</span>
                    @empty
                        <span class="text-xs text-slate-500">Belum mencantumkan skill.</span>
                    @endforelse
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @if ($applicant->profile?->cv_path)
                        <a href="{{ asset('storage/'.$applicant->profile->cv_path) }}" target="_blank" rel="noopener" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Lihat CV</a>
                    @endif
                    @if ($application->status === 'pending')
                        <form method="POST" action="{{ route('project-owner.pelamar.status', $application) }}">
                            @csrf
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" @disabled($full) class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">Terima</button>
                        </form>
                        <form method="POST" action="{{ route('project-owner.pelamar.status', $application) }}">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Tolak</button>
                        </form>
                        @if ($full)
                            <span class="text-xs text-rose-600">Kuota penuh ({{ $application->project->team_members_count }}/{{ $application->project->max_members }})</span>
                        @endif
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-slate-200 bg-white px-5 py-10 text-center text-sm text-slate-500 shadow-sm">Belum ada pelamar.</div>
        @endforelse
    </div>
@endsection
