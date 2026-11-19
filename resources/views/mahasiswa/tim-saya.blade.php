@extends('mahasiswa.layout')

@section('title', 'Tim Saya')

@section('content')
    <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-6">Tim Saya</h2>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Aplikasi Kasir UMKM</h3>
            <p class="text-xs text-slate-500">Owner: [Nama]. Anggota: 4 orang.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" class="py-2.5 px-5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-xl text-sm transition">
                Laporkan
            </button>
            <button type="button" class="py-2.5 px-5 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
                Beri ulasan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h4 class="text-sm font-bold text-slate-900 mb-4">To Do</h4>
            <div class="space-y-3">
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-xl text-xs font-medium text-slate-700">
                    Susun kuesioner
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h4 class="text-sm font-bold text-slate-900 mb-4">In Progress</h4>
            <div class="space-y-3">
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-xl text-xs font-medium text-slate-700">
                    Wireframe halaman utama
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h4 class="text-sm font-bold text-slate-900 mb-4">Done</h4>
            <div class="space-y-3">
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-xl text-xs font-medium text-slate-700">
                    Laporan minggu 1
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h4 class="text-sm font-bold text-slate-900 mb-4">Anggota tim</h4>
        <div class="flex items-center gap-2 flex-wrap text-xs">
            <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-semibold rounded-full border border-indigo-100">
                [Owner]
            </span>
            <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-semibold rounded-full border border-indigo-100">
                [Anggota 1]
            </span>
            <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-semibold rounded-full border border-indigo-100">
                [Anggota 2]
            </span>
            <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-semibold rounded-full border border-indigo-100">
                Saya
            </span>
        </div>
    </div>
@endsection