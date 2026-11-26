@extends('mahasiswa.layout')

@section('title', 'Profil dan Skill')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 id="page-title" class="text-3xl font-bold text-slate-900 tracking-tight">Profil dan Skill</h2>

        <button type="button" id="btn-edit-mode" onclick="toggleEditMode(true)" class="py-2 px-5 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
            Edit profil
        </button>

        <div id="edit-actions" class="hidden items-center gap-2">
            <button type="button" onclick="toggleEditMode(false)" class="py-2 px-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-xl text-xs transition">
                Batal
            </button>
            <button type="submit" form="profile-form" class="py-2 px-4 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
                Simpan perubahan
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <form id="profile-form" action="{{ route('mahasiswa.profil-skill.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 lg:col-span-7">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                @if ($user->profile?->avatar)
                    <img src="{{ asset('storage/' . $user->profile->avatar) }}" alt="Foto {{ $user->name }}" class="w-14 h-14 rounded-full object-cover shrink-0 border border-slate-200">
                @else
                    <div class="w-14 h-14 rounded-full bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center text-lg shrink-0">
                        {{ strtoupper(substr($user->name ?? 'NM', 0, 2)) }}
                    </div>
                @endif

                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-slate-900 view-mode">{{ $user->name ?? '[Nama Mahasiswa]' }}</h3>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-slate-900 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden edit-mode mb-1">
                    <p class="text-xs text-slate-400 truncate">{{ $user->email ?? '[email]' }}</p>
                </div>

                <div id="avatar-action" class="hidden">
                    <label class="cursor-pointer px-3 py-1.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-lg text-xs transition inline-block">
                        Ganti foto
                        <input type="file" name="avatar" accept="image/jpg,image/jpeg,image/png,image/webp" class="hidden">
                    </label>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">NIM</span>
                    <p class="text-sm font-medium text-slate-800 view-mode">{{ $user->profile?->nim ?: '[NIM]' }}</p>
                    <input type="text" name="nim" value="{{ old('nim', $user->profile?->nim) }}" placeholder="Masukkan NIM" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden edit-mode">
                </div>

                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">No. HP</span>
                    <p class="text-sm font-medium text-slate-800 view-mode">{{ $user->profile?->phone ?: '[No. HP]' }}</p>
                    <input type="text" name="phone" value="{{ old('phone', $user->profile?->phone) }}" placeholder="Masukkan No. HP" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden edit-mode">
                </div>

                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Bio</span>
                    <p class="text-sm font-medium text-slate-800 view-mode">{{ $user->profile?->bio ?: '[Bio singkat mahasiswa]' }}</p>
                    <input type="text" name="bio" value="{{ old('bio', $user->profile?->bio) }}" placeholder="Tulis bio singkat" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden edit-mode">
                </div>

                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Curriculum Vitae (CV)</span>

                    <div class="view-mode">
                        @if ($user->profile?->cv_path)
                            <a href="{{ route('mahasiswa.profil-skill.cv') }}" target="_blank"
                                class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:underline">
                                Lihat / Unduh CV (PDF)
                            </a>
                        @else
                            <p class="text-sm font-medium text-amber-600">Belum diunggah (wajib untuk melamar proyek)</p>
                        @endif
                    </div>

                    <div class="hidden edit-mode">
                        <input type="file" name="cv" accept="application/pdf"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-xl p-1.5">
                        <p class="text-[11px] text-slate-400 mt-1">
                            Format PDF, maksimal 5MB.
                            @if ($user->profile?->cv_path)
                                Biarkan kosong jika tidak ingin mengganti CV saat ini.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </form>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 lg:col-span-5">
            <h3 class="text-base font-bold text-slate-900 mb-4">Skill saya</h3>

            <div class="divide-y divide-slate-100">
                @forelse ($user->skills as $skill)
                    <div class="py-3 flex items-center justify-between gap-2 first:pt-0">
                        <span class="text-sm font-medium text-slate-800">{{ $skill->name }}</span>

                        <span class="px-3 py-1 bg-slate-100 text-slate-600 font-medium text-xs rounded-full view-mode">
                            {{ $skill->pivot->level }}
                        </span>

                        <div class="hidden edit-mode flex items-center gap-2">
                            <form action="{{ route('mahasiswa.profil-skill.skills.update', $skill) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="level" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-300 text-slate-700 text-xs rounded-lg focus:outline-none">
                                    @foreach (['Mahir', 'Menengah', 'Pemula'] as $levelOption)
                                        <option value="{{ $levelOption }}" @selected($skill->pivot->level === $levelOption)>
                                            {{ $levelOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>

                            <form action="{{ route('mahasiswa.profil-skill.skills.destroy', $skill) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-white border border-slate-300 text-red-600 hover:bg-red-50 font-medium rounded-lg text-xs transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="py-3 text-xs text-slate-400">Belum ada skill yang ditambahkan.</p>
                @endforelse
            </div>

            <div id="add-skill-section" class="mt-4 pt-4 border-t border-slate-100 hidden">
                <form action="{{ route('mahasiswa.profil-skill.skills.store') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <select name="skill_id" required class="flex-1 px-3 py-2 bg-slate-50 border border-slate-300 text-slate-700 text-xs rounded-xl focus:outline-none">
                        <option value="">Pilih skill dari daftar</option>
                        @foreach ($allSkills as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <select name="level" required class="px-3 py-2 bg-slate-50 border border-slate-300 text-slate-700 text-xs rounded-xl focus:outline-none">
                        <option value="Pemula">Pemula</option>
                        <option value="Menengah">Menengah</option>
                        <option value="Mahir">Mahir</option>
                    </select>
                    <button type="submit" class="py-2 px-4 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm shrink-0">
                        Tambah
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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

    @if ($errors->any() || session('keep_editing'))
        document.addEventListener('DOMContentLoaded', () => toggleEditMode(true));
    @endif
</script>
@endpush