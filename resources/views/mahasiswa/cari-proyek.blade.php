@extends('mahasiswa.layout')

@section('title', 'Cari Proyek')

@section('content')
    <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-6">Cari Proyek</h2>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-8">
        <form action="{{ route('mahasiswa.cari-proyek') }}" method="GET" class="flex items-center gap-3 max-w-4xl">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari proyek"
                class="flex-1 px-4 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white shadow-sm">
            
            @if (request('category_id'))
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            @endif
            @if (request('skill_id'))
                <input type="hidden" name="skill_id" value="{{ request('skill_id') }}">
            @endif
            @if (request('available_only'))
                <input type="hidden" name="available_only" value="1">
            @endif

            <button type="submit" class="py-3 px-6 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
                Cari
            </button>
        </form>

        <form id="filter-form" action="{{ route('mahasiswa.cari-proyek') }}" method="GET" class="flex items-center gap-2 mt-4 flex-wrap text-xs">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if (request('available_only'))
                <input type="hidden" name="available_only" value="1">
            @endif

            <div class="relative inline-flex items-center">
                <select name="category_id" onchange="this.form.submit()" aria-label="Filter Kategori"
                    class="appearance-none cursor-pointer pl-3.5 pr-7 py-1.5 rounded-full border font-semibold transition focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ request('category_id') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-indigo-50 text-indigo-700 border-indigo-100 hover:bg-indigo-100' }}">
                    <option value="" class="bg-white text-slate-800">Kategori: Semua</option>
                    @foreach ($categories ?? collect() as $category)
                        <option value="{{ $category->id }}" class="bg-white text-slate-800" @selected(request('category_id') == $category->id)>
                            Kategori: {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute right-2.5 text-[10px] {{ request('category_id') ? 'text-white' : 'text-indigo-700' }}">▼</span>
            </div>

            <div class="relative inline-flex items-center">
                <select name="skill_id" onchange="this.form.submit()" aria-label="Filter Skill"
                    class="appearance-none cursor-pointer pl-3.5 pr-7 py-1.5 rounded-full border font-semibold transition focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ request('skill_id') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-indigo-50 text-indigo-700 border-indigo-100 hover:bg-indigo-100' }}">
                    <option value="" class="bg-white text-slate-800">Skill: Semua</option>
                    @foreach ($allSkills ?? collect() as $skill)
                        <option value="{{ $skill->id }}" class="bg-white text-slate-800" @selected(request('skill_id') == $skill->id)>
                            Skill: {{ $skill->name }}
                        </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute right-2.5 text-[10px] {{ request('skill_id') ? 'text-white' : 'text-indigo-700' }}">▼</span>
            </div>

            <a href="{{ route('mahasiswa.cari-proyek', array_merge(request()->except('page'), ['available_only' => request('available_only') ? null : 1])) }}"
                class="px-3.5 py-1.5 rounded-full border font-semibold transition {{ request('available_only') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-indigo-50 text-indigo-700 border-indigo-100 hover:bg-indigo-100' }}">
                Kuota tersedia
            </a>

            @if (request()->hasAny(['search', 'category_id', 'skill_id', 'available_only']))
                <a href="{{ route('mahasiswa.cari-proyek') }}"
                    class="px-3 py-1.5 rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-medium transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        @forelse ($projects ?? collect() as $project)
            @php
                $isMatched = $project->skills->pluck('id')->intersect($userSkillIds ?? collect())->isNotEmpty();
                $skillNames = $project->skills->pluck('name')->implode(', ');
            @endphp

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    @if ($isMatched)
                        <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full mb-3">
                            Cocok dengan skill-mu
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full mb-3">
                            {{ $project->category?->name ?? 'Proyek' }}
                        </span>
                    @endif

                    <h3 class="text-lg font-bold text-slate-900 leading-snug mb-2">{{ $project->name }}</h3>
                    <p class="text-xs text-slate-500">
                        Kuota {{ $project->max_members ?? 0 }}.
                        @if ($skillNames)
                            Skill: {{ $skillNames }}.
                        @endif
                    </p>
                </div>

                <div class="mt-6">
                    @if ($hasActiveProject ?? false)
                        <button type="button" disabled
                            class="py-2 px-5 bg-slate-200 text-slate-500 font-medium rounded-xl text-sm cursor-not-allowed"
                            title="Selesaikan proyek aktifmu terlebih dahulu">
                            Sedang dalam proyek
                        </button>
                    @elseif (!($hasCv ?? false))
                        <a href="{{ route('mahasiswa.profil-skill') }}"
                            class="inline-block py-2 px-5 bg-amber-50 border border-amber-200 text-amber-700 hover:bg-amber-100 font-medium rounded-xl text-sm transition">
                            Lengkapi CV untuk Apply
                        </a>
                    @else
                        <button type="button"
                            data-title="{{ $project->name }}"
                            data-action="{{ route('mahasiswa.cari-proyek.apply', $project) }}"
                            onclick="openModalFromButton(this)"
                            class="py-2 px-5 btn-indigo-custom text-white font-medium rounded-xl text-sm transition shadow-sm">
                            Apply
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-8 rounded-2xl border border-slate-200 text-center text-sm text-slate-500">
                Belum ada proyek yang sesuai dengan pencarian atau filter.
            </div>
        @endforelse
    </div>

    <div id="applyModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-xl w-full p-6 lg:p-8">
            <h3 class="text-xl font-bold text-slate-900 mb-1">Modal Apply</h3>
            <p id="modalProjectTitle" class="text-xs text-indigo-600 font-semibold mb-6"></p>

            <form id="applyForm" action="#" method="POST">
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
@endsection

@push('scripts')
<script>
    function openModalFromButton(button) {
        const projectTitle = button.getAttribute('data-title');
        const actionUrl = button.getAttribute('data-action');

        document.getElementById('modalProjectTitle').innerText = 'Melamar untuk: ' + projectTitle;
        document.getElementById('applyForm').setAttribute('action', actionUrl);
        document.getElementById('applyModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('applyModal').classList.add('hidden');
    }
</script>
@endpush