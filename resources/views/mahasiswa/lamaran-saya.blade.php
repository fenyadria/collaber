<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamaran Saya - Collaber</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-navy-custom { background-color: #0b132b; }
        .btn-indigo-custom { background-color: #3b45e3; }
        .btn-indigo-custom:hover { background-color: #313ac9; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased">

    <div class="min-h-screen flex">

        <aside class="w-64 bg-navy-custom text-white flex flex-col justify-between hidden md:flex shrink-0">
            <div>
                <div class="p-6">
                    <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight hover:text-slate-200 transition">
                        Collaber
                    </a>
                </div>

                <nav class="mt-2 px-3 space-y-1">
                    <a href="{{ url('/mahasiswa/dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ url('/mahasiswa/cari-proyek') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Cari Proyek</span>
                    </a>
                    <a href="{{ url('/mahasiswa/lamaran-saya') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-medium text-sm transition">
                        <span>Lamaran Saya</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Tim Saya</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Profil dan Skill</span>
                    </a>
                </nav>
            </div>

            <div class="p-4 border-t border-slate-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2.5 rounded-lg text-red-400 hover:bg-red-500/10 text-sm font-medium transition flex items-center gap-2">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-6 lg:p-10 overflow-y-auto">
            
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-6">Lamaran Saya</h2>

            <div class="flex items-center gap-2 mb-8 flex-wrap">
                <button class="px-4 py-1.5 bg-indigo-100 text-indigo-700 font-semibold rounded-full text-xs transition">
                    Semua
                </button>
                <button class="px-4 py-1.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-medium rounded-full text-xs transition">
                    pending
                </button>
                <button class="px-4 py-1.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-medium rounded-full text-xs transition">
                    accepted
                </button>
                <button class="px-4 py-1.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-medium rounded-full text-xs transition">
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
                                    <button class="py-2 px-4 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
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
                                    <button class="py-2 px-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-xl text-xs transition">
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
                                    <button class="py-2 px-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-xl text-xs transition">
                                        Lihat
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

        </main>

    </div>

</body>
</html>