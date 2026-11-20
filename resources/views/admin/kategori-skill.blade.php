@extends('admin.layout')

@section('title', 'Kategori dan Skill')

@section('content')
    <header class="mb-7">
        <p class="mb-1 text-sm font-medium text-slate-500">Collaber / Admin</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Kategori dan Skill</h1>
        <p class="mt-1 text-sm text-slate-500">Kelola pilihan kategori proyek dan master skill.</p>
    </header>

    @if (session('success'))
        <p role="status" class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</p>
    @endif
    @error('name')
        <p role="alert" class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $message }}</p>
    @enderror
    @error('delete')
        <p role="alert" class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ $message }}</p>
    @enderror

    <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-2">
        @foreach ([
            ['heading' => 'Kategori proyek', 'items' => $categories, 'type' => 'kategori'],
            ['heading' => 'Master skill', 'items' => $skills, 'type' => 'skill'],
        ] as $manager)
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" data-manager data-type="{{ $manager['type'] }}" data-store-url="{{ route('admin.catalog.store', $manager['type']) }}">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="font-semibold text-slate-900">{{ $manager['heading'] }}</h2>
                    <button type="button" data-add class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700 sm:text-sm">+ Tambah</button>
                </div>
                <ul class="divide-y divide-slate-100" data-items>
                    @forelse ($manager['items'] as $item)
                        <li class="flex items-center justify-between gap-3 py-3" data-item data-item-id="{{ $item->id }}" data-update-url="{{ route('admin.catalog.update', [$manager['type'], $item->id]) }}">
                            <span class="min-w-0 truncate text-sm text-slate-700" data-item-name>{{ $item->name }}</span>
                            <div class="flex shrink-0 gap-2">
                                <button type="button" data-edit class="rounded-md border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Edit</button>
                                <form method="POST" action="{{ route('admin.catalog.delete', [$manager['type'], $item->id]) }}" data-delete-form>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 transition hover:bg-rose-50 hover:text-rose-700">Hapus</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li data-no-items class="py-3 text-sm text-slate-500">Belum ada data.</li>
                    @endforelse
                </ul>
            </section>
        @endforeach
    </div>

    <dialog id="item-dialog" class="w-[calc(100%-2rem)] max-w-md rounded-xl border border-slate-200 p-0 shadow-xl backdrop:bg-slate-900/40">
        <form id="item-form" method="POST" class="p-6">
            @csrf
            <input id="method-override" type="hidden" name="_method" value="" disabled>
            <input id="catalog-type" type="hidden" name="_catalog_type" value="">
            <input id="catalog-item" type="hidden" name="_catalog_item" value="">
            <h2 id="dialog-title" class="text-lg font-semibold text-slate-900">Tambah data</h2>
            <label for="item-name" class="mt-5 block text-sm font-medium text-slate-700">Nama</label>
            <input id="item-name" name="name" required maxlength="80" value="{{ old('name') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" id="cancel-dialog" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
    <script>
        const dialog = document.querySelector('#item-dialog');
        const form = document.querySelector('#item-form');
        const nameInput = document.querySelector('#item-name');
        const dialogTitle = document.querySelector('#dialog-title');
        const methodOverride = document.querySelector('#method-override');
        const catalogType = document.querySelector('#catalog-type');
        const catalogItem = document.querySelector('#catalog-item');

        function openItemDialog(manager, item = null) {
            form.action = item ? item.dataset.updateUrl : manager.dataset.storeUrl;
            methodOverride.value = item ? 'PUT' : '';
            methodOverride.disabled = !item;
            catalogType.value = manager.dataset.type;
            catalogItem.value = item ? item.dataset.itemId : '';
            dialogTitle.textContent = item ? 'Edit data' : 'Tambah data';
            nameInput.value = item ? item.querySelector('[data-item-name]').textContent.trim() : '';
            dialog.showModal();
            nameInput.focus();
        }

        document.querySelectorAll('[data-manager]').forEach((manager) => {
            manager.querySelector('[data-add]').addEventListener('click', () => openItemDialog(manager));
            manager.addEventListener('click', (event) => {
                const button = event.target.closest('[data-edit]');
                if (button) {
                    openItemDialog(manager, button.closest('[data-item]'));
                }
            });
            manager.addEventListener('submit', (event) => {
                if (event.target.matches('[data-delete-form]') && !window.confirm('Hapus item ini?')) {
                    event.preventDefault();
                }
            });
        });

        const validationType = @json(old('_catalog_type'));
        const validationItem = @json(old('_catalog_item'));
        @if ($errors->has('name'))
            const failedManager = [...document.querySelectorAll('[data-manager]')]
                .find((manager) => manager.dataset.type === validationType);
            if (failedManager) {
                const failedItem = validationItem
                    ? [...failedManager.querySelectorAll('[data-item]')]
                        .find((item) => item.dataset.itemId === String(validationItem))
                    : null;
                openItemDialog(failedManager, failedItem ?? null);
                nameInput.value = @json(old('name'));
            }
        @endif

        document.querySelector('#cancel-dialog').addEventListener('click', () => dialog.close());
        dialog.addEventListener('close', () => form.reset());
    </script>
@endpush
