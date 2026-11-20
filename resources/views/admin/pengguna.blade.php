@extends('admin.layout')

@section('title', 'Manajemen Pengguna')

@section('content')
    <header class="mb-7">
        <p class="mb-1 text-sm font-medium text-slate-500">Collaber / Admin</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Manajemen Pengguna</h1>
        <p class="mt-1 text-sm text-slate-500">Cari pengguna dan kelola status akun platform.</p>
    </header>

    @if (session('success'))
        <p role="status" class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</p>
    @endif
    @error('status')
        <p role="alert" class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $message }}</p>
    @enderror

    <form method="GET" action="{{ route('admin.pengguna') }}" class="mb-5 flex flex-col gap-3 sm:flex-row">
        <label class="sr-only" for="user-search">Cari nama atau email</label>
        <input id="user-search" type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau email" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 sm:max-w-sm">
        <label class="sr-only" for="user-role-filter">Filter role</label>
        <select id="user-role-filter" name="role" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500">
            <option value="">Semua role</option>
            <option value="mahasiswa" @selected($filterRole === 'mahasiswa')>Mahasiswa</option>
            <option value="project_owner" @selected($filterRole === 'project_owner')>Project Owner</option>
            <option value="admin" @selected($filterRole === 'admin')>Admin</option>
        </select>
        <label class="sr-only" for="user-status-filter">Filter status</label>
        <select id="user-status-filter" name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500">
            <option value="">Semua status</option>
            <option value="active" @selected($filterStatus === 'active')>Aktif</option>
            <option value="inactive" @selected($filterStatus === 'inactive')>Nonaktif</option>
        </select>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">Cari</button>
    </form>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-4">Nama</th>
                        <th scope="col" class="px-5 py-4">Email</th>
                        <th scope="col" class="px-5 py-4">Role</th>
                        <th scope="col" class="px-5 py-4">Status</th>
                        <th scope="col" class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $managedUser)
                        <tr>
                            <td class="px-5 py-4 font-medium text-slate-800">{{ $managedUser->name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $managedUser->email }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ str($managedUser->role)->replace('_', ' ')->title() }}</td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-emerald-50 text-emerald-700' => $managedUser->status === 'active',
                                    'bg-slate-100 text-slate-600' => $managedUser->status !== 'active',
                                ])>{{ $managedUser->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.pengguna.show', $managedUser) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Detail</a>
                                    @if ($managedUser->is(auth()->user()))
                                        <span class="text-xs text-slate-500">Akun saat ini</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.pengguna.status', $managedUser) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $managedUser->status === 'active' ? 'inactive' : 'active' }}">
                                            <button type="submit" class="rounded-md {{ $managedUser->status === 'active' ? 'border border-slate-300 text-slate-700 hover:bg-rose-50 hover:text-rose-700' : 'bg-indigo-600 text-white hover:bg-indigo-700' }} px-3 py-1.5 text-xs font-semibold transition">
                                                {{ $managedUser->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Tidak ada pengguna yang cocok dengan pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $users->links() }}</div>
        @endif
    </section>
@endsection
