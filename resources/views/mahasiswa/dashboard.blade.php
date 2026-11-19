@extends('mahasiswa.layout')

@section('title', 'Dashboard Mahasiswa')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Halo, {{ $user->name ?? 'Mahasiswa' }}</h2>
        <p class="text-sm text-slate-500 mt-1">Ringkasan aktivitasmu di Collaber.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-sm text-slate-500 font-medium">Lamaran pending</span>
            <span class="text-4xl font-bold text-slate-900 mt-4">2</span>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-sm text-slate-500 font-medium">Diterima</span>
            <span class="text-4xl font-bold text-emerald-600 mt-4">1</span>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-sm text-slate-500 font-medium">Ditolak</span>
            <span class="text-4xl font-bold text-rose-600 mt-4">0</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-5">Tugas saya</h3>

            <div class="divide-y divide-slate-100">
                <div class="py-3.5 flex items-center justify-between">
                    <span class="text-sm text-slate-700">Buat wireframe halaman utama</span>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full">In Progress</span>
                </div>
                <div class="py-3.5 flex items-center justify-between">
                    <span class="text-sm text-slate-700">Susun kuesioner survei</span>
                    <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">To Do</span>
                </div>
                <div class="py-3.5 flex items-center justify-between">
                    <span class="text-sm text-slate-700">Rapikan laporan minggu 1</span>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-full">Done</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-5">Rekomendasi sesuai skill</h3>

                <div class="space-y-4">
                    <div class="pb-4 border-b border-slate-100">
                        <h4 class="text-sm font-semibold text-slate-800">Redesain Aplikasi Event Kampus</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Cocok: Figma</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-800">Sistem Informasi Perpustakaan</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Cocok: PHP, MySQL</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ url('/mahasiswa/cari-proyek') }}"
                    class="w-full py-2.5 px-4 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm text-center block">
                    Lihat semua proyek
                </a>
            </div>
        </div>
    </div>
@endsection