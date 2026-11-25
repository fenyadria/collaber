@extends('admin.layout')

@section('title', 'Manajemen Laporan')

@section('content')
    <header class="mb-7">
        <p class="mb-1 text-sm font-medium text-slate-500">Collaber / Admin</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Laporan Pelanggaran</h1>
        <p class="mt-1 text-sm text-slate-500">Tinjau laporan dan catat tindak lanjutnya.</p>
    </header>

    @if (session('success'))
        <p role="status" class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</p>
    @endif

    <form method="GET" action="{{ route('admin.laporan') }}" class="mb-5 flex flex-col gap-3 sm:flex-row">
        <label class="sr-only" for="report-search">Cari laporan atau pelapor</label>
        <input id="report-search" type="search" name="q" value="{{ $search }}" placeholder="Cari isi laporan atau pelapor" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 sm:max-w-md">
        <label class="sr-only" for="report-status-filter">Filter status</label>
        <select id="report-status-filter" name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500">
            <option value="">Semua status</option>
            @foreach ($statusLabels as $value => $label)
                <option value="{{ $value }}" @selected($filterStatus === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">Cari</button>
    </form>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-4">Pelapor</th>
                        <th scope="col" class="px-5 py-4">Isi laporan</th>
                        <th scope="col" class="px-5 py-4">Tanggal</th>
                        <th scope="col" class="px-5 py-4">Status</th>
                        <th scope="col" class="px-5 py-4">Tindak lanjut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reports as $report)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-800">{{ $report->reporter_name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $report->reporter_email }}</p>
                            </td>
                            <td class="max-w-md px-5 py-4 text-slate-600">
                                <details>
                                    <summary class="cursor-pointer font-medium text-indigo-700">Lihat detail laporan</summary>
                                    <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $report->content }}</p>
                                </details>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ \Illuminate\Support\Carbon::parse($report->created_at)->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-blue-50 text-blue-700' => $report->status === 'pending',
                                    'bg-amber-50 text-amber-700' => $report->status === 'in_review',
                                    'bg-emerald-50 text-emerald-700' => $report->status === 'resolved',
                                    'bg-slate-100 text-slate-600' => $report->status === 'dismissed' || !isset($statusLabels[$report->status]),
                                ])>{{ $statusLabels[$report->status] ?? str($report->status)->replace('_', ' ')->title() }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.laporan.status', $report->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    <label class="sr-only" for="report-status-{{ $report->id }}">Status tindak lanjut</label>
                                    <select id="report-status-{{ $report->id }}" name="status" class="rounded-md border border-slate-300 bg-white px-2 py-1.5 text-xs">
                                        @foreach ($statusLabels as $value => $label)
                                            <option value="{{ $value }}" @selected($report->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada laporan yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reports->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $reports->links() }}</div>
        @endif
    </section>
@endsection
