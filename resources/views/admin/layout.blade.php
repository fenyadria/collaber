<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Collaber Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #f4f7fc; }
        .admin-sidebar { background: #111d36; }
        .admin-active { background: #3e50c8; }
    </style>
</head>
<body class="min-h-screen font-sans text-slate-800 antialiased">
    <div class="min-h-screen md:flex">
        <aside class="admin-sidebar flex w-full shrink-0 flex-col text-white md:min-h-screen md:w-56 md:justify-between lg:w-64">
            <div>
                <div class="px-6 py-5">
                    <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold tracking-tight">Collaber <span class="text-blue-300">Admin</span></a>
                </div>
                <nav aria-label="Navigasi Admin" class="flex gap-1 overflow-x-auto px-3 pb-3 md:flex-col md:overflow-visible">
                    <a href="{{ route('admin.dashboard') }}" @class(['admin-active' => request()->routeIs('admin.dashboard'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Dashboard</a>
                    <a href="{{ route('admin.verifikasi-proyek') }}" @class(['admin-active' => request()->routeIs('admin.verifikasi-proyek'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Verifikasi Proyek</a>
                    <a href="{{ route('admin.kategori-skill') }}" @class(['admin-active' => request()->routeIs('admin.kategori-skill'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Kategori dan Skill</a>
                    <span class="rounded-lg px-3 py-2.5 text-sm text-slate-500" aria-disabled="true">Pengguna</span>
                    <span class="rounded-lg px-3 py-2.5 text-sm text-slate-500" aria-disabled="true">Laporan</span>
                </nav>
            </div>
            <div class="hidden border-t border-slate-700/70 p-4 md:block">
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
