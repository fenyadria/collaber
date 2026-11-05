@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <header class="mb-7">
        <p class="mb-1 text-sm font-medium text-slate-500">Collaber / Admin</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Dashboard Admin</h1>
        <p class="mt-1 text-sm text-slate-500">Ringkasan aktivitas dan kebutuhan moderasi platform.</p>
    </header>

    <section aria-label="Statistik" class="mb-7 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ($statistics as $statistic)
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-medium text-slate-500">{{ $statistic['label'] }}</h2>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $statistic['value'] }}</p>
            </article>
        @endforeach
    </section>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-slate-900">Proyek menunggu verifikasi</h2>
                    <p class="mt-1 text-sm text-slate-500">Proyek baru yang perlu ditinjau.</p>
                </div>
                <a href="{{ route('admin.verifikasi-proyek') }}" class="shrink-0 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700 sm:text-sm">Buka antrean</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach ($pendingProjects as $project)
                    <li class="flex flex-col gap-1 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <span class="text-sm font-medium text-slate-800">{{ $project['name'] }}</span>
                        <span class="text-xs text-slate-500">Owner: {{ $project['owner'] }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-slate-900">Laporan terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">Laporan yang baru dikirim.</p>
                </div>
                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ count($latestReports) }} baru</span>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach ($latestReports as $report)
                    <li class="py-3">
                        <p class="text-sm font-medium text-slate-800">{{ $report['title'] }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $report['project'] }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
@endsection
