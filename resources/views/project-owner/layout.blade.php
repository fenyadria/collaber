<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Collaber Project Owner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #f4f7fc; }
        .po-sidebar { background: #111d36; }
        .po-active { background: #3e50c8; }
    </style>
</head>
<body class="min-h-screen font-sans text-slate-800 antialiased">
    <div class="min-h-screen md:flex">
        <aside class="po-sidebar flex w-full shrink-0 flex-col text-white md:min-h-screen md:w-56 md:justify-between lg:w-64">
            <div>
                <div class="px-6 py-5">
                    <a href="{{ route('project-owner.dashboard') }}" class="text-lg font-bold tracking-tight">Collaber</a>
                </div>
                <nav aria-label="Navigasi Project Owner" class="flex gap-1 overflow-x-auto px-3 pb-3 md:flex-col md:overflow-visible">
                    <a href="{{ route('project-owner.dashboard') }}" @class(['po-active' => request()->routeIs('project-owner.dashboard'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Dashboard</a>
                    <a href="{{ route('project-owner.proyek-saya') }}" @class(['po-active' => request()->routeIs('project-owner.proyek-saya'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Proyek Saya</a>
                    <a href="{{ route('project-owner.posting-proyek') }}" @class(['po-active' => request()->routeIs('project-owner.posting-proyek*'), 'rounded-lg px-3 py-2.5 text-sm text-slate-200 transition hover:bg-slate-700/70' => true])>Posting Proyek</a>
                    <span class="rounded-lg px-3 py-2.5 text-sm text-slate-500" aria-disabled="true">Pelamar</span>
                    <span class="rounded-lg px-3 py-2.5 text-sm text-slate-500" aria-disabled="true">Workspace</span>
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
