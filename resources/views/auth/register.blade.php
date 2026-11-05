<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun - Collaber</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-navy-custom { background-color: #0b132b; }
        .btn-indigo-custom { background-color: #3b45e3; }
        .btn-indigo-custom:hover { background-color: #313ac9; }
    </style>
</head>
<body class="bg-white font-sans antialiased">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">

        <div class="lg:col-span-5 bg-navy-custom text-white p-10 lg:p-16 flex flex-col justify-between">
            <div>
                <a href="{{ url('/') }}" class="text-2xl font-bold tracking-tight hover:text-slate-200 transition inline-block">
                    Collaber
                </a>
            </div>
            
            <div class="my-auto py-10">
                <h2 class="text-4xl lg:text-5xl font-bold tracking-tight mb-4">Mulai berkolaborasi</h2>
                <p class="text-slate-300 text-base leading-relaxed">
                    Daftar sebagai Mahasiswa untuk mencari proyek, atau sebagai Project Owner untuk menemukan anggota tim.
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-400">Platform pencarian partner proyek mahasiswa.</p>
            </div>
        </div>

        <div class="lg:col-span-7 bg-white p-8 lg:px-20 lg:py-12 flex flex-col justify-center overflow-y-auto">
            <div class="max-w-md w-full mx-auto">
                
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Buat akun</h2>
                    <p class="text-sm text-slate-500 mt-1">Pilih peranmu terlebih dahulu.</p>
                </div>

                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ url('/register') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-4">
                        <label id="card-mahasiswa" onclick="selectRole('mahasiswa')" class="cursor-pointer border-2 border-indigo-600 bg-indigo-50/40 p-4 rounded-xl transition flex flex-col justify-between">
                            <input type="radio" name="role" value="mahasiswa" checked class="hidden">
                            <div>
                                <span class="font-semibold text-slate-900 text-sm block">Mahasiswa</span>
                                <span class="text-xs text-slate-500 mt-0.5 block leading-relaxed">Cari dan ikuti proyek</span>
                            </div>
                        </label>

                        <label id="card-owner" onclick="selectRole('project_owner')" class="cursor-pointer border-2 border-slate-200 hover:border-slate-300 p-4 rounded-xl transition flex flex-col justify-between bg-white">
                            <input type="radio" name="role" value="project_owner" class="hidden">
                            <div>
                                <span class="font-semibold text-slate-900 text-sm block">Project Owner</span>
                                <span class="text-xs text-slate-500 mt-0.5 block leading-relaxed">Posting proyek dan rekrut tim</span>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Nama kamu">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="nama@email.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Minimal 8 karakter">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Ulangi password">
                    </div>

                    <button type="submit" 
                        class="w-full py-3 px-4 btn-indigo-custom text-white font-medium rounded-lg text-sm transition shadow-md mt-2">
                        Daftar
                    </button>
                </form>

                <p class="text-center text-sm text-slate-500 mt-6">
                    Sudah punya akun? <a href="{{ url('/login') }}" class="text-indigo-600 font-medium hover:underline">Masuk</a>
                </p>

            </div>
        </div>

    </div>

    <!-- Skrip Interaktif untuk Pemilihan Role Card -->
    <script>
        function selectRole(role) {
            const cardMhs = document.getElementById('card-mahasiswa');
            const cardOwner = document.getElementById('card-owner');
            
            if(role === 'mahasiswa') {
                cardMhs.className = "cursor-pointer border-2 border-indigo-600 bg-indigo-50/40 p-4 rounded-xl transition flex flex-col justify-between";
                cardOwner.className = "cursor-pointer border-2 border-slate-200 hover:border-slate-300 p-4 rounded-xl transition flex flex-col justify-between bg-white";
                cardMhs.querySelector('input').checked = true;
            } else {
                cardOwner.className = "cursor-pointer border-2 border-indigo-600 bg-indigo-50/40 p-4 rounded-xl transition flex flex-col justify-between";
                cardMhs.className = "cursor-pointer border-2 border-slate-200 hover:border-slate-300 p-4 rounded-xl transition flex flex-col justify-between bg-white";
                cardOwner.querySelector('input').checked = true;
            }
        }
    </script>
</body>
</html>