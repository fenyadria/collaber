<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil dan Skill - Collaber</title>
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
                    <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('mahasiswa.cari-proyek') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Cari Proyek</span>
                    </a>
                    <a href="{{ route('mahasiswa.lamaran-saya') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Lamaran Saya</span>
                    </a>
                    <a href="{{ route('mahasiswa.tim-saya') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium text-sm transition">
                        <span>Tim Saya</span>
                    </a>
                    <a href="{{ route('mahasiswa.profil-skill') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-medium text-sm transition">
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
            
            <div class="flex items-center justify-between mb-6">
                <h2 id="page-title" class="text-3xl font-bold text-slate-900 tracking-tight">Profil dan Skill</h2>
                
                <button id="btn-edit-mode" onclick="toggleEditMode(true)" class="py-2 px-5 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
                    Edit profil
                </button>

                <div id="edit-actions" class="hidden items-center gap-2">
                    <button onclick="toggleEditMode(false)" class="py-2 px-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-xl text-xs transition">
                        Batal
                    </button>
                    <button onclick="toggleEditMode(false)" class="py-2 px-4 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
                        Simpan perubahan
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 lg:col-span-7">
                    
                    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                        <div class="w-14 h-14 rounded-full bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center text-lg shrink-0">
                            NM
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-slate-900">[Nama Mahasiswa]</h3>
                            <p class="text-xs text-slate-400">[email]</p>
                        </div>
                        <div id="avatar-action" class="hidden">
                            <button class="px-3 py-1.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-lg text-xs transition">
                                Ganti foto
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">NIM</span>

                            <p class="text-sm font-medium text-slate-800 view-mode">[NIM]</p>
                           
                            <input type="text" value="[NIM]" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden edit-mode">
                        </div>

                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">No. HP</span>
                            
                            <p class="text-sm font-medium text-slate-800 view-mode">[No. HP]</p>
                           
                            <input type="text" value="[No. HP]" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden edit-mode">
                        </div>

                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Bio</span>
                         
                            <p class="text-sm font-medium text-slate-800 view-mode">[Bio singkat mahasiswa]</p>
                       
                            <input type="text" value="[Bio singkat mahasiswa]" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden edit-mode">
                        </div>
                    </div>

                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 lg:col-span-5">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Skill saya</h3>

                    <div class="divide-y divide-slate-100">
                        
                        <div class="py-3 flex items-center justify-between first:pt-0">
                            <span class="text-sm font-medium text-slate-800">Figma</span>
                          
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 font-medium text-xs rounded-full view-mode">Mahir</span>
                       
                            <div class="hidden edit-mode flex items-center gap-2">
                                <select class="px-3 py-1.5 bg-slate-50 border border-slate-300 text-slate-700 text-xs rounded-lg focus:outline-none">
                                    <option selected>Mahir</option>
                                    <option>Menengah</option>
                                    <option>Pemula</option>
                                </select>
                                <button class="px-3 py-1.5 bg-white border border-slate-300 text-red-600 hover:bg-red-50 font-medium rounded-lg text-xs transition">Hapus</button>
                            </div>
                        </div>

                        <div class="py-3 flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-800">PHP</span>
                     
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 font-medium text-xs rounded-full view-mode">Menengah</span>
                  
                            <div class="hidden edit-mode flex items-center gap-2">
                                <select class="px-3 py-1.5 bg-slate-50 border border-slate-300 text-slate-700 text-xs rounded-lg focus:outline-none">
                                    <option>Mahir</option>
                                    <option selected>Menengah</option>
                                    <option>Pemula</option>
                                </select>
                                <button class="px-3 py-1.5 bg-white border border-slate-300 text-red-600 hover:bg-red-50 font-medium rounded-lg text-xs transition">Hapus</button>
                            </div>
                        </div>

                        <div class="py-3 flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-800">Statistik</span>
                   
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 font-medium text-xs rounded-full view-mode">Pemula</span>
                    
                            <div class="hidden edit-mode flex items-center gap-2">
                                <select class="px-3 py-1.5 bg-slate-50 border border-slate-300 text-slate-700 text-xs rounded-lg focus:outline-none">
                                    <option>Mahir</option>
                                    <option>Menengah</option>
                                    <option selected>Pemula</option>
                                </select>
                                <button class="px-3 py-1.5 bg-white border border-slate-300 text-red-600 hover:bg-red-50 font-medium rounded-lg text-xs transition">Hapus</button>
                            </div>
                        </div>

                    </div>

                    <div id="add-skill-section" class="mt-4 pt-4 border-t border-slate-100 hidden">
                        <div class="flex items-center gap-2">
                            <select class="flex-1 px-3 py-2 bg-slate-50 border border-slate-300 text-slate-500 text-xs rounded-xl focus:outline-none">
                                <option>Pilih skill dari daftar</option>
                            </select>
                            <button class="py-2 px-4 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
                                Tambah
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </main>

    </div>

    <script>
        function toggleEditMode(isEdit) {
            const pageTitle = document.getElementById('page-title');
            const btnEdit = document.getElementById('btn-edit-mode');
            const editActions = document.getElementById('edit-actions');
            const avatarAction = document.getElementById('avatar-action');
            const addSkillSection = document.getElementById('add-skill-section');
            
            const viewElements = document.querySelectorAll('.view-mode');
            const editElements = document.querySelectorAll('.edit-mode');

            if (isEdit) {
                pageTitle.innerText = "Edit Profil dan Skill";
                btnEdit.classList.add('hidden');
                editActions.classList.remove('hidden');
                editActions.classList.add('flex');
                avatarAction.classList.remove('hidden');
                addSkillSection.classList.remove('hidden');

                viewElements.forEach(el => el.classList.add('hidden'));
                editElements.forEach(el => el.classList.remove('hidden'));
            } else {
                pageTitle.innerText = "Profil dan Skill";
                btnEdit.classList.remove('hidden');
                editActions.classList.add('hidden');
                editActions.classList.remove('flex');
                avatarAction.classList.add('hidden');
                addSkillSection.classList.add('hidden');

                viewElements.forEach(el => el.classList.remove('hidden'));
                editElements.forEach(el => el.classList.add('hidden'));
            }
        }
    </script>

</body>
</html>