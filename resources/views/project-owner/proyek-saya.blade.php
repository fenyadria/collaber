@extends('project-owner.layout')

@section('title', 'Proyek Saya')

@section('content')
    <header class="mb-5 flex flex-wrap items-start justify-between gap-3">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Proyek Saya</h1>
        <a href="{{ route('project-owner.posting-proyek') }}" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">+ Posting proyek baru</a>
    </header>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
    @endif

    <nav aria-label="Filter status" class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('project-owner.proyek-saya') }}" @class(['rounded-full px-3 py-1 text-xs font-semibold', 'bg-indigo-600 text-white' => ! $filter, 'bg-indigo-50 text-indigo-800' => $filter])>Semua</a>
        @foreach ($statuses as $status)
            <a href="{{ route('project-owner.proyek-saya', ['status' => $status]) }}" @class(['rounded-full px-3 py-1 text-xs font-semibold', 'bg-indigo-600 text-white' => $filter === $status, 'bg-indigo-50 text-indigo-800' => $filter !== $status])>{{ $status }}</a>
        @endforeach
    </nav>

    <section class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="border-b border-slate-200 text-slate-500">
                <tr>
                    <th class="px-5 py-3 font-medium">Proyek</th>
                    <th class="px-3 py-3 font-medium">Kategori</th>
                    <th class="px-3 py-3 font-medium">Kuota terisi</th>
                    <th class="px-3 py-3 font-medium">Pelamar</th>
                    <th class="px-3 py-3 font-medium">Verifikasi</th>
                    <th class="px-3 py-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($projects as $project)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $project['name'] }}</td>
                        <td class="px-3 py-4 text-slate-600">{{ $project['category'] }}</td>
                        <td class="px-3 py-4 text-slate-600">{{ $project['filled'] }} dari {{ $project['quota'] }}</td>
                        <td class="px-3 py-4 text-slate-600">{{ $project['applicants'] }}</td>
                        <td class="px-3 py-4">@include('project-owner._status-badge', ['status' => $project['status']])</td>
                        <td class="px-3 py-4">
                            <div class="flex flex-wrap gap-2">
                                @if ($project['status'] === 'approved')
                                    <span class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white opacity-60" aria-disabled="true">Pelamar</span>
                                    <span class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 opacity-60" aria-disabled="true">Workspace</span>
                                @else
                                    <span class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 opacity-60" aria-disabled="true">Edit</span>
                                @endif
                                @if ($project['status'] === 'rejected')
                                    <button type="button" data-reason-toggle="reason-{{ $loop->index }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Lihat alasan</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada proyek dengan status ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    @foreach ($projects as $project)
        @if ($project['status'] === 'rejected')
            <section id="reason-{{ $loop->index }}" class="mt-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900">Alasan penolakan: {{ $project['name'] }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $project['reason'] }}</p>
            </section>
        @endif
    @endforeach
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-reason-toggle]').forEach(function (button) {
            var panel = document.getElementById(button.dataset.reasonToggle);
            panel.hidden = true;
            button.addEventListener('click', function () { panel.hidden = ! panel.hidden; });
        });
    </script>
@endpush
