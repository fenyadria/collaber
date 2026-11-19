<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Mahasiswa') - Collaber</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #f4f7fc; }
        .mhs-sidebar { background: #111d36; }
        .mhs-active { background: #3e50c8; }
        .btn-indigo-custom { background-color: #3b45e3; }
        .btn-indigo-custom:hover { background-color: #313ac9; }
    </style>
</head>
<body class="min-h-screen font-sans text-slate-800 antialiased">
    <div class="min-h-screen md:flex">
        <aside class="mhs-sidebar flex w-full shrink-0 flex-col text-white md:min-h-screen md:w-56 md:justify-between lg:w-64">
            <div>
                <div class="px-6 py-5">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="text-lg font-bold tracking-tight">Collaber</a>
                </div>
                <nav aria-label="Navigasi Mahasiswa" class="flex gap-1 overflow-x-auto px-3 pb-3 md:flex-col md:overflow-visible">
                    <a href="{{ route('mahasiswa.dashboard') }}" @class(['mhs-active' => request()->routeIs('mahasiswa.dashboard'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Dashboard</a>
                    <a href="{{ route('mahasiswa.cari-proyek') }}" @class(['mhs-active' => request()->routeIs('mahasiswa.cari-proyek'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Cari Proyek</a>
                    <a href="{{ route('mahasiswa.lamaran-saya') }}" @class(['mhs-active' => request()->routeIs('mahasiswa.lamaran-saya'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Lamaran Saya</a>
                    <a href="{{ route('mahasiswa.tim-saya') }}" @class(['mhs-active' => request()->routeIs('mahasiswa.tim-saya'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Tim Saya</a>
                    <a href="{{ route('mahasiswa.profil-skill') }}" @class(['mhs-active' => request()->routeIs('mahasiswa.profil-skill'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Profil dan Skill</a>
                </nav>
            </div>
            <div class="border-t border-slate-700/70 p-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-slate-300 transition hover:bg-slate-700 hover:text-white">Keluar</button>
                </form>
            </div>
        </aside>

        <main class="min-w-0 flex-1 p-5 sm:p-7 lg:p-10">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>