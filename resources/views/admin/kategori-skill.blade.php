@extends('admin.layout')

@section('title', 'Kategori dan Skill')

@section('content')
    <header class="mb-7">
        <p class="mb-1 text-sm font-medium text-slate-500">Collaber / Admin</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Kategori dan Skill</h1>
        <p class="mt-1 text-sm text-slate-500">Kelola pilihan kategori proyek dan master skill.</p>
    </header>

    <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-2">
        @foreach (['Kategori proyek' => $categories, 'Master skill' => $skills] as $heading => $items)
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" data-manager>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="font-semibold text-slate-900">{{ $heading }}</h2>
                    <button type="button" data-add class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700 sm:text-sm">+ Tambah</button>
                </div>
                <ul class="divide-y divide-slate-100" data-items>
                    @foreach ($items as $item)
                        <li class="flex items-center justify-between gap-3 py-3" data-item>
                            <span class="min-w-0 truncate text-sm text-slate-700" data-item-name>{{ $item }}</span>
                            <div class="flex shrink-0 gap-2">
                                <button type="button" data-edit class="rounded-md border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Edit</button>
                                <button type="button" data-delete class="rounded-md border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 transition hover:bg-rose-50 hover:text-rose-700">Hapus</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <p data-empty class="hidden py-3 text-sm text-slate-500">Belum ada data.</p>
            </section>
        @endforeach
    </div>

    <dialog id="item-dialog" class="w-[calc(100%-2rem)] max-w-md rounded-xl border border-slate-200 p-0 shadow-xl backdrop:bg-slate-900/40">
        <form id="item-form" class="p-6">
            <h2 id="dialog-title" class="text-lg font-semibold text-slate-900">Tambah data</h2>
            <label for="item-name" class="mt-5 block text-sm font-medium text-slate-700">Nama</label>
            <input id="item-name" name="name" required maxlength="80" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
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
        let currentList;
        let currentItem;

        function openItemDialog(list, item = null) {
            currentList = list;
            currentItem = item;
            dialogTitle.textContent = item ? 'Edit data' : 'Tambah data';
            nameInput.value = item ? item.querySelector('[data-item-name]').textContent.trim() : '';
            dialog.showModal();
            nameInput.focus();
        }

        document.querySelectorAll('[data-manager]').forEach((manager) => {
            const list = manager.querySelector('[data-items]');
            const emptyMessage = manager.querySelector('[data-empty]');

            manager.querySelector('[data-add]').addEventListener('click', () => openItemDialog(list));
            manager.addEventListener('click', (event) => {
                const button = event.target.closest('button');
                if (!button) return;

                const item = button.closest('[data-item]');
                if (button.matches('[data-edit]')) {
                    openItemDialog(list, item);
                } else if (button.matches('[data-delete]') && window.confirm('Hapus item ini?')) {
                    item.remove();
                    emptyMessage.classList.toggle('hidden', list.children.length > 0);
                }
            });
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const name = nameInput.value.trim();
            if (!name) {
                nameInput.focus();
                return;
            }

            if (currentItem) {
                currentItem.querySelector('[data-item-name]').textContent = name;
            } else {
                const item = document.createElement('li');
                item.className = 'flex items-center justify-between gap-3 py-3';
                item.dataset.item = '';
                const label = document.createElement('span');
                label.className = 'min-w-0 truncate text-sm text-slate-700';
                label.dataset.itemName = '';
                label.textContent = name;
                const actions = document.createElement('div');
                actions.className = 'flex shrink-0 gap-2';
                actions.innerHTML = '<button type="button" data-edit class="rounded-md border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Edit</button><button type="button" data-delete class="rounded-md border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 transition hover:bg-rose-50 hover:text-rose-700">Hapus</button>';
                item.append(label, actions);
                currentList.append(item);
            }

            currentList.closest('[data-manager]').querySelector('[data-empty]').classList.add('hidden');
            dialog.close();
            form.reset();
        });

        document.querySelector('#cancel-dialog').addEventListener('click', () => dialog.close());
        dialog.addEventListener('close', () => form.reset());
    </script>
@endpush
