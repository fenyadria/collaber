@extends('admin.layout')

@section('title', 'Verifikasi Proyek')

@section('content')
    <header class="mb-7">
        <p class="mb-1 text-sm font-medium text-slate-500">Collaber / Admin</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Antrean verifikasi proyek</h1>
        <p class="mt-1 text-sm text-slate-500">Tinjau proyek yang diajukan sebelum ditampilkan kepada mahasiswa.</p>
    </header>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-4">Nama Proyek</th>
                        <th scope="col" class="px-5 py-4">Owner</th>
                        <th scope="col" class="px-5 py-4">Kategori</th>
                        <th scope="col" class="px-5 py-4">Status</th>
                        <th scope="col" class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="project-list" class="divide-y divide-slate-100">
                    @foreach ($projects as $index => $project)
                        <tr>
                            <td class="px-5 py-4 font-medium text-slate-800">{{ $project['name'] }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $project['owner'] }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $project['category'] }}</td>
                            <td class="px-5 py-4">
                                <span data-status class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">Pending</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <button type="button" data-decision="Approved" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700">Approve</button>
                                    <button type="button" data-decision="Rejected" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">Reject</button>
                                </div>
                                <span class="sr-only" data-announcement role="status" aria-live="polite"></span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('#project-list [data-decision]').forEach((button) => {
            button.addEventListener('click', () => {
                const row = button.closest('tr');
                const status = row.querySelector('[data-status]');
                const decision = button.dataset.decision;
                const approved = decision === 'Approved';

                status.textContent = approved ? 'Approved' : 'Rejected';
                status.className = approved
                    ? 'rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700'
                    : 'rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700';
                row.querySelector('[data-announcement]').textContent = `Status proyek diubah menjadi ${decision}.`;
                row.querySelectorAll('[data-decision]').forEach((action) => {
                    action.disabled = true;
                    action.classList.add('cursor-not-allowed', 'opacity-50');
                });
            });
        });
    </script>
@endpush
