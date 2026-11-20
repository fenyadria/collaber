@extends('admin.layout')

@section('title', 'Detail Pengguna')

@section('content')
    <header class="mb-7">
        <p class="mb-1 text-sm font-medium text-slate-500">Collaber / Admin / Pengguna</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Detail Pengguna</h1>
    </header>

    <section class="max-w-2xl rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <dl class="divide-y divide-slate-100">
            <div class="grid gap-1 py-4 sm:grid-cols-3">
                <dt class="text-sm font-medium text-slate-500">Nama</dt>
                <dd class="text-sm font-semibold text-slate-800 sm:col-span-2">{{ $managedUser->name }}</dd>
            </div>
            <div class="grid gap-1 py-4 sm:grid-cols-3">
                <dt class="text-sm font-medium text-slate-500">Email</dt>
                <dd class="text-sm text-slate-800 sm:col-span-2">{{ $managedUser->email }}</dd>
            </div>
            <div class="grid gap-1 py-4 sm:grid-cols-3">
                <dt class="text-sm font-medium text-slate-500">Role</dt>
                <dd class="text-sm text-slate-800 sm:col-span-2">{{ str($managedUser->role)->replace('_', ' ')->title() }}</dd>
            </div>
            <div class="grid gap-1 py-4 sm:grid-cols-3">
                <dt class="text-sm font-medium text-slate-500">Status akun</dt>
                <dd class="text-sm text-slate-800 sm:col-span-2">{{ $managedUser->status === 'active' ? 'Aktif' : 'Nonaktif' }}</dd>
            </div>
            <div class="grid gap-1 py-4 sm:grid-cols-3">
                <dt class="text-sm font-medium text-slate-500">Terdaftar</dt>
                <dd class="text-sm text-slate-800 sm:col-span-2">{{ $managedUser->created_at->format('d M Y, H:i') }}</dd>
            </div>
        </dl>
        <a href="{{ route('admin.pengguna') }}" class="mt-5 inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali ke pengguna</a>
    </section>
@endsection
