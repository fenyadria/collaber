<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Proyek - Collaber</title>
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
                    <a href="{{ url('/mahasiswa/cari-proyek') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-medium text-sm transition">
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
            
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-6">Cari Proyek</h2>

            <div class="mb-8">
                <form action="#" method="GET" class="flex items-center gap-3 max-w-4xl">
                    <input type="text" name="search" placeholder="Cari proyek"
                        class="flex-1 px-4 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white shadow-sm">
                    <button type="submit" class="py-3 px-6 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
                        Cari
                    </button>
                </form>

                <div class="flex items-center gap-2 mt-4 flex-wrap text-xs">
                    <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-semibold rounded-full border border-indigo-100">
                        Kategori: Semua
                    </span>
                    <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-semibold rounded-full border border-indigo-100">
                        Skill: Figma
                    </span>
                    <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-semibold rounded-full border border-indigo-100">
                        Kuota tersedia
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
 
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full mb-3">
                            Cocok dengan skill-mu
                        </span>
                        <h3 class="text-lg font-bold text-slate-900 leading-snug mb-2">Redesain Aplikasi Event Kampus</h3>
                        <p class="text-xs text-slate-500">Kuota 3. Skill: Figma, Riset UX.</p>
                    </div>
                    <div class="mt-6">
                        <button onclick="openModal('Redesain Aplikasi Event Kampus')" class="py-2 px-5 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
                            Apply
                        </button>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full mb-3">
                            Cocok dengan skill-mu
                        </span>
                        <h3 class="text-lg font-bold text-slate-900 leading-snug mb-2">Sistem Informasi Perpustakaan</h3>
                        <p class="text-xs text-slate-500">Kuota 4. Skill: PHP, MySQL.</p>
                    </div>
                    <div class="mt-6">
                        <button onclick="openModal('Sistem Informasi Perpustakaan')" class="py-2 px-5 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
                            Apply
                        </button>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full mb-3">
                            Penelitian
                        </span>
                        <h3 class="text-lg font-bold text-slate-900 leading-snug mb-2">Survei Pola Belajar Mahasiswa</h3>
                        <p class="text-xs text-slate-500">Kuota 5. Skill: Statistik, Penulisan.</p>
                    </div>
                    <div class="mt-6">
                        <button onclick="openModal('Survei Pola Belajar Mahasiswa')" class="py-2 px-5 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
                            Apply
                        </button>
                    </div>
                </div>

            </div>

        </main>

    </div>

    <div id="applyModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-xl w-full p-6 lg:p-8">
            
            <h3 class="text-xl font-bold text-slate-900 mb-1">Modal Apply</h3>
            <p id="modalProjectTitle" class="text-xs text-indigo-600 font-semibold mb-6"></p>

            <form action="#" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pesan motivasi</label>
                    <input type="text" name="motivation" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        placeholder="Ceritakan kenapa kamu cocok di proyek ini">
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeModal()" class="py-2.5 px-5 bg-white border border-slate-300 text-slate-700 font-medium rounded-xl text-sm hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="py-2.5 px-5 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
                        Kirim lamaran
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function openModal(projectTitle) {
            document.getElementById('modalProjectTitle').innerText = 'Melamar untuk: ' + projectTitle;
            document.getElementById('applyModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('applyModal').classList.add('hidden');
        }
    </script>

</body>
</html>