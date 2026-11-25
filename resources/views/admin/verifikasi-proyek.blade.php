@extends('admin.layout')

@section('title', 'Verifikasi Proyek')

@section('content')
    <header class="mb-7">
        <p class="mb-1 text-sm font-medium text-slate-500">Collaber / Admin</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Antrean verifikasi proyek</h1>
        <p class="mt-1 text-sm text-slate-500">Tinjau proyek yang diajukan sebelum ditampilkan kepada mahasiswa.</p>
    </header>

    @if (session('success'))
        <p role="status" class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</p>
    @endif
    @error('project')
        <p role="alert" class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ $message }}</p>
    @enderror

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
                    @forelse ($projects as $project)
                        <tr>
                            <td class="px-5 py-4 font-medium text-slate-800">{{ $project->name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $project->owner_name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $project->category_name }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">Pending</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('admin.verifikasi-proyek.status', $project->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.verifikasi-proyek.status', $project->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Tidak ada proyek yang menunggu verifikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($projects->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $projects->links() }}</div>
        @endif
    </section>
@endsection
