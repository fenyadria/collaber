@extends('project-owner.layout')

@section('title', 'Dashboard')

@section('content')
    <header class="mb-7">
        <p class="mb-1 text-sm font-medium text-slate-500">Collaber / Project Owner</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Dashboard Project Owner</h1>
    </header>

    <section aria-label="Statistik" class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($statistics as $statistic)
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-medium text-slate-500">{{ $statistic['label'] }}</h2>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $statistic['value'] }}</p>
            </article>
        @endforeach
    </section>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="mb-4 font-semibold text-slate-900">Proyek saya</h2>
            <ul class="divide-y divide-slate-100">
                @foreach ($projects as $project)
                    <li class="flex items-center gap-2 py-3">
                        <span class="text-sm font-medium text-slate-800">{{ $project['name'] }}</span>
                        @include('project-owner._status-badge', ['status' => $project['status']])
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="mb-4 font-semibold text-slate-900">Pelamar terbaru</h2>
            <ul class="divide-y divide-slate-100">
                @foreach ($applicants as $applicant)
                    <li class="py-3">
                        <p class="text-sm font-medium text-slate-800">{{ $applicant['name'] }}</p>
                        <p class="mt-1 text-xs text-slate-500">Melamar: {{ $applicant['project'] }}</p>
                    </li>
                @endforeach
            </ul>
            <span class="mt-4 inline-block cursor-not-allowed rounded-lg bg-indigo-300 px-3 py-2 text-sm font-semibold text-white" aria-disabled="true">Lihat semua pelamar</span>
        </section>
    </div>
@endsection
