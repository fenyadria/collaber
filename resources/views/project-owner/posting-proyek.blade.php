@extends('project-owner.layout')

@php
    $editing = $project !== null;
    $selectedSkills = old('skills', $editing ? $project->skills->pluck('name')->all() : []);
@endphp

@section('title', $editing ? 'Edit Proyek' : 'Posting Proyek')

@section('content')
    <h1 class="mb-5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $editing ? 'Edit Proyek' : 'Posting Proyek Baru' }}</h1>

    @include('project-owner._flash')

    @if ($editing && $project->status === 'rejected')
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
            <p class="font-semibold">Proyek ini ditolak admin.</p>
            <p class="mt-1">Revisi proyek lalu kirim ulang untuk verifikasi.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
        <form action="{{ $editing ? route('project-owner.proyek.update', $project) : route('project-owner.posting-proyek.store') }}" method="POST" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="name" class="mb-1 block text-sm font-semibold">Judul proyek</label>
                <input id="name" name="name" type="text" value="{{ old('name', $project?->name) }}" placeholder="Contoh: Aplikasi Kasir UMKM" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="description" class="mb-1 block text-sm font-semibold">Deskripsi</label>
                <textarea id="description" name="description" rows="4" placeholder="Jelaskan tujuan dan ruang lingkup proyek" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">{{ old('description', $project?->description) }}</textarea>
            </div>

            <div>
                <label for="category_id" class="mb-1 block text-sm font-semibold">Kategori</label>
                <select id="category_id" name="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id', $project?->category_id) === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="max_members" class="mb-1 block text-sm font-semibold">Kuota anggota</label>
                    <input id="max_members" name="max_members" type="number" min="1" max="50" value="{{ old('max_members', $project?->max_members ?? 4) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="deadline" class="mb-1 block text-sm font-semibold">Tenggat</label>
                    <input id="deadline" name="deadline" type="date" value="{{ old('deadline', $project?->deadline?->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <fieldset>
                <legend class="mb-2 text-sm font-semibold">Skill dibutuhkan</legend>
                <div id="skill-list" class="flex flex-wrap gap-2">
                    @foreach ($skills as $skill)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="skills[]" value="{{ $skill }}" class="peer sr-only" @checked(in_array($skill, $selectedSkills, true))>
                            <span class="inline-block rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800 peer-checked:bg-indigo-600 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-400">{{ $skill }}</span>
                        </label>
                    @endforeach
                    <button type="button" id="add-skill" class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800 transition hover:bg-indigo-100">+ Tambah</button>
                </div>
            </fieldset>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" name="action" value="draft" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Simpan draf</button>
                <button type="submit" name="action" value="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">{{ $editing && $project->status === 'rejected' ? 'Kirim ulang untuk verifikasi' : 'Kirim untuk verifikasi' }}</button>
            </div>
        </form>

        <div class="space-y-5">
            <aside class="h-fit rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-2 font-semibold text-slate-900">Alur verifikasi</h2>
                <ol class="list-inside list-decimal space-y-1 text-sm text-slate-600">
                    <li>Kirim proyek, status pending.</li>
                    <li>Admin meninjau.</li>
                    <li>Jika approved, proyek tampil di katalog.</li>
                    <li>Jika rejected, kamu menerima alasan dan bisa merevisi.</li>
                </ol>
            </aside>

            @if ($editing)
                <form action="{{ route('project-owner.proyek.destroy', $project) }}" method="POST" onsubmit="return confirm('Hapus proyek ini? Tindakan tidak dapat dibatalkan.')" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    @csrf
                    @method('DELETE')
                    <h2 class="mb-2 font-semibold text-slate-900">Hapus proyek</h2>
                    <p class="mb-3 text-sm text-slate-600">Proyek beserta lamarannya akan dihapus permanen.</p>
                    <button type="submit" class="rounded-lg border border-rose-300 px-3 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">Hapus proyek</button>
                </form>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('add-skill').addEventListener('click', function () {
            var name = (window.prompt('Nama skill:') || '').trim();
            if (! name) return;
            var label = document.createElement('label');
            label.className = 'cursor-pointer';
            var input = document.createElement('input');
            input.type = 'checkbox'; input.name = 'skills[]'; input.value = name; input.checked = true;
            input.className = 'peer sr-only';
            var chip = document.createElement('span');
            chip.className = 'inline-block rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800 peer-checked:bg-indigo-600 peer-checked:text-white';
            chip.textContent = name;
            label.append(input, chip);
            this.before(label);
        });
    </script>
@endpush
