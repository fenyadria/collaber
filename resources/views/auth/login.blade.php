<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Collaber</title>
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
                <h2 class="text-4xl lg:text-5xl font-bold tracking-tight mb-4">Selamat datang kembali</h2>
                <p class="text-slate-300 text-base leading-relaxed">
                    Masuk untuk melanjutkan proyek, lamaran, dan tim kamu.
                </p>

                <!-- Panduan Akun Dummy Sesuai AuthController Kamu -->
                <div class="mt-8 p-4 bg-slate-800/60 rounded-xl border border-slate-700 text-xs text-slate-300 space-y-2">
                    <p class="font-semibold text-white">💡 Kredensial Uji Coba:</p>
                    <div class="space-y-1">
                        <p>• Admin: <code class="text-blue-300">admin@collaber.test</code> / <code class="text-yellow-300">admin123</code></p>
                        <p>• Owner: <code class="text-blue-300">owner@collaber.test</code> / <code class="text-yellow-300">owner123</code></p>
                        <p>• Mahasiswa: <code class="text-blue-300">mahasiswa@collaber.test</code> / <code class="text-yellow-300">mahasiswa123</code></p>
                    </div>
                </div>
            </div>

            <div>
                <p class="text-xs text-slate-400">Platform pencarian partner proyek mahasiswa.</p>
            </div>
        </div>

        <div class="lg:col-span-7 bg-white p-8 lg:p-24 flex flex-col justify-center">
            <div class="max-w-md w-full mx-auto">
                
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Masuk</h2>
                    <p class="text-sm text-slate-500 mt-1">Kamu akan diarahkan ke dashboard sesuai peranmu.</p>
                </div>

                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ url('/login') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="nama@email.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Masukkan password">
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center text-slate-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full py-3 px-4 btn-indigo-custom text-white font-medium rounded-lg text-sm transition shadow-md">
                        Masuk
                    </button>
                </form>

                <p class="text-center text-sm text-slate-500 mt-8">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:underline">Daftar</a>
                </p>

            </div>
        </div>

    </div>

</body>
</html>