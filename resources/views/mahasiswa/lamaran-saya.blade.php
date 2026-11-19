@extends('mahasiswa.layout')

@section('title', 'Lamaran Saya')

@section('content')
    <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-6">Lamaran Saya</h2>

    <div class="flex items-center gap-2 mb-8 flex-wrap">
        <button type="button" class="px-4 py-1.5 bg-indigo-100 text-indigo-700 font-semibold rounded-full text-xs transition">
            Semua
        </button>
        <button type="button" class="px-4 py-1.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-medium rounded-full text-xs transition">
            pending
        </button>
        <button type="button" class="px-4 py-1.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-medium rounded-full text-xs transition">
            accepted
        </button>
        <button type="button" class="px-4 py-1.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-medium rounded-full text-xs transition">
            rejected
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 text-xs font-semibold tracking-wider">
                        <th class="py-4 px-6">Proyek</th>
                        <th class="py-4 px-6">Owner</th>
                        <th class="py-4 px-6">Tanggal</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-semibold text-slate-900">Aplikasi Kasir UMKM</td>
                        <td class="py-4 px-6 text-slate-500">[Owner]</td>
                        <td class="py-4 px-6 text-slate-500">[tanggal]</td>
                        <td class="py-4 px-6">
                            <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 font-semibold text-xs rounded-full">
                                accepted
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button type="button" class="py-2 px-4 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
                                Buka tim
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-semibold text-slate-900">Redesain Aplikasi Event Kampus</td>
                        <td class="py-4 px-6 text-slate-500">[Owner]</td>
                        <td class="py-4 px-6 text-slate-500">[tanggal]</td>
                        <td class="py-4 px-6">
                            <span class="inline-block px-3 py-1 bg-indigo-50/60 text-indigo-600 font-medium text-xs rounded-full">
                                pending
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button type="button" class="py-2 px-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-xl text-xs transition">
                                Lihat
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-semibold text-slate-900">Film Pendek Dokumenter</td>
                        <td class="py-4 px-6 text-slate-500">[Owner]</td>
                        <td class="py-4 px-6 text-slate-500">[tanggal]</td>
                        <td class="py-4 px-6">
                            <span class="inline-block px-3 py-1 bg-indigo-50/40 text-indigo-400 font-medium text-xs rounded-full">
                                rejected
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button type="button" class="py-2 px-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-xl text-xs transition">
                                Lihat
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection