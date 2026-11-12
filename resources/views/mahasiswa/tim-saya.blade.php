<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tim Saya - Collaber</title>
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
                    <a href="{{ url('/mahasiswa/lamaran-saya') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Lamaran Saya</span>
                    </a>
                    <a href="{{ url('/mahasiswa/tim-saya') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-medium text-sm transition">
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
            
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-6">Tim Saya</h2>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Aplikasi Kasir UMKM</h3>
                    <p class="text-xs text-slate-500">Owner: [Nama]. Anggota: 4 orang.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="py-2.5 px-5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-xl text-sm transition">
                        Laporkan
                    </button>
                    <button class="py-2.5 px-5 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
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

        </main>

    </div>

</body>
</html>