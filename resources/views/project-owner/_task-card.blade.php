{{-- Kartu tugas untuk owner. Butuh: $task, $teamMembers --}}
<li class="rounded-lg border border-slate-200 bg-slate-50 p-3">
    <p class="text-sm font-semibold text-slate-800">{{ $task->title }}</p>
    @if ($task->description)
        <p class="mt-1 text-xs text-slate-600">{{ $task->description }}</p>
    @endif
    <p class="mt-2 text-xs text-slate-500">
        {{ $task->assignees->pluck('name')->join(', ') ?: 'Belum ditugaskan' }}
        @if ($task->deadline) &middot; {{ $task->deadline->format('d M Y') }} @endif
    </p>

    <form method="POST" action="{{ route('project-owner.workspace.tasks.status', $task) }}" class="mt-2">
        @csrf
        @method('PATCH')
        <select name="status" onchange="this.form.submit()" aria-label="Ubah status tugas" class="w-full cursor-pointer rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700">
            <option value="todo" @selected($task->status === 'todo')>Pindah ke: To do</option>
            <option value="in_progress" @selected($task->status === 'in_progress')>Pindah ke: Dikerjakan</option>
            <option value="done" @selected($task->status === 'done')>Pindah ke: Selesai</option>
        </select>
    </form>

    <div class="mt-3 border-t border-slate-200 pt-2.5">
        @if ($task->submission_path)
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-2">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-emerald-700">&#10003; Hasil kerja diunggah</span>
                    <a href="{{ asset('storage/'.$task->submission_path) }}" target="_blank" rel="noopener" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat file</a>
                </div>
                @if ($task->submission_note)
                    <p class="mt-1 break-words text-xs text-slate-600">{{ $task->submission_note }}</p>
                @endif
            </div>
        @else
            <p class="text-xs font-medium text-amber-600">Belum ada hasil diunggah</p>
        @endif

        <details class="mt-2">
            <summary class="cursor-pointer select-none text-xs font-medium text-slate-500 hover:text-indigo-600">{{ $task->submission_path ? 'Ganti hasil kerja' : 'Unggah hasil kerja' }}</summary>
            <form method="POST" action="{{ route('project-owner.workspace.tasks.submission', $task) }}" enctype="multipart/form-data" class="mt-2 space-y-1.5">
                @csrf
                <input type="file" name="submission" required accept=".pdf,.zip,.png,.jpg,.jpeg,.webp,.doc,.docx" class="w-full text-xs text-slate-600 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-2.5 file:py-1 file:text-xs file:font-semibold file:text-indigo-700">
                <input type="text" name="submission_note" maxlength="255" value="{{ $task->submission_note }}" placeholder="Catatan / link pendukung (opsional)" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs">
                <button type="submit" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100">Simpan hasil kerja</button>
            </form>
        </details>
    </div>

    <details class="mt-2">
        <summary class="cursor-pointer select-none text-xs font-medium text-slate-500 hover:text-indigo-600">Edit tugas</summary>
        <form method="POST" action="{{ route('project-owner.workspace.tasks.update', $task) }}" class="mt-2 space-y-2">
            @csrf
            @method('PUT')
            <input type="text" name="title" required maxlength="255" value="{{ $task->title }}" aria-label="Judul tugas" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs">
            <textarea name="description" rows="2" maxlength="2000" aria-label="Deskripsi tugas" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs">{{ $task->description }}</textarea>
            <input type="date" name="deadline" value="{{ $task->deadline?->format('Y-m-d') }}" aria-label="Tenggat" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs">
            <div class="flex flex-wrap gap-1.5">
                @foreach ($teamMembers as $member)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="assignees[]" value="{{ $member->user_id }}" class="peer sr-only" @checked($task->assignees->contains('id', $member->user_id))>
                        <span class="inline-block rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-800 peer-checked:bg-indigo-600 peer-checked:text-white">{{ $member->user->name }}</span>
                    </label>
                @endforeach
            </div>
            <button type="submit" class="w-full rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700">Simpan perubahan</button>
        </form>
    </details>

    <form method="POST" action="{{ route('project-owner.workspace.tasks.destroy', $task) }}" onsubmit="return confirm('Hapus tugas ini?')" class="mt-2">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Hapus</button>
    </form>
</li>
