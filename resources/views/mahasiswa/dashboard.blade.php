<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - Collaber</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-navy-custom { background-color: #0b132b; }
        .bg-navy-sidebar { background-color: #0e1726; }
        .btn-indigo-custom { background-color: #3b45e3; }
        .btn-indigo-custom:hover { background-color: #313ac9; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased">

    <div class="min-h-screen flex">

        <aside class="w-64 bg-navy-custom text-white flex flex-col justify-between hidden md:flex shrink-0">
            <div>
                <div class="p-6">
                    <h1 class="text-xl font-bold tracking-tight">Collaber</h1>
                </div>

                <nav class="mt-2 px-3 space-y-1">
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-medium text-sm transition">
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ url('/mahasiswa/cari-proyek') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Cari Proyek</span>
                    </a>
                    <a href="{{ url('/mahasiswa/lamaran-saya') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Lamaran Saya</span>
                    </a>
                    <a href="{{ url('/mahasiswa/tim-saya') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Tim Saya</span>
                    </a>
                    <a href="{{ url('/mahasiswa/profil-skill') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
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
  
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Halo, {{ $user['name'] ?? 'Mahasiswa' }}</h2>
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

        </main>

    </div>

</body>
</html>