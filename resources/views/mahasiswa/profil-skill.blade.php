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
            <button type="button" onclick="toggleEditMode(false)" class="py-2 px-4 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
                Simpan perubahan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 lg:col-span-7">
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                <div class="w-14 h-14 rounded-full bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center text-lg shrink-0">
                    {{ strtoupper(substr($user->name ?? 'NM', 0, 2)) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-bold text-slate-900">{{ $user->name ?? '[Nama Mahasiswa]' }}</h3>
                    <p class="text-xs text-slate-400">{{ $user->email ?? '[email]' }}</p>
                </div>
                <div id="avatar-action" class="hidden">
                    <button type="button" class="px-3 py-1.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-lg text-xs transition">
                        Ganti foto
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">NIM</span>
                    <p class="text-sm font-medium text-slate-800 view-mode">{{ $user->profile?->nim ?? '[NIM]' }}</p>
                    <input type="text" value="{{ $user->profile?->nim ?? '' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden edit-mode">
                </div>

                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">No. HP</span>
                    <p class="text-sm font-medium text-slate-800 view-mode">{{ $user->profile?->phone ?? '[No. HP]' }}</p>
                    <input type="text" value="{{ $user->profile?->phone ?? '' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden edit-mode">
                </div>

                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Bio</span>
                    <p class="text-sm font-medium text-slate-800 view-mode">{{ $user->profile?->bio ?? '[Bio singkat mahasiswa]' }}</p>
                    <input type="text" value="{{ $user->profile?->bio ?? '' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden edit-mode">
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
                        <button type="button" class="px-3 py-1.5 bg-white border border-slate-300 text-red-600 hover:bg-red-50 font-medium rounded-lg text-xs transition">Hapus</button>
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
                        <button type="button" class="px-3 py-1.5 bg-white border border-slate-300 text-red-600 hover:bg-red-50 font-medium rounded-lg text-xs transition">Hapus</button>
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
                        <button type="button" class="px-3 py-1.5 bg-white border border-slate-300 text-red-600 hover:bg-red-50 font-medium rounded-lg text-xs transition">Hapus</button>
                    </div>
                </div>
            </div>

            <div id="add-skill-section" class="mt-4 pt-4 border-t border-slate-100 hidden">
                <div class="flex items-center gap-2">
                    <select class="flex-1 px-3 py-2 bg-slate-50 border border-slate-300 text-slate-500 text-xs rounded-xl focus:outline-none">
                        <option>Pilih skill dari daftar</option>
                    </select>
                    <button type="button" class="py-2 px-4 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
                        Tambah
                    </button>
                </div>
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
</script>
@endpush