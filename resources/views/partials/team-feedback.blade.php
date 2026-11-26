{{--
    Dialog "Laporkan" dan "Beri ulasan" untuk satu proyek.
    Butuh: $project, $others (daftar pengguna lain di proyek yang bisa dilaporkan/diulas).
    Buka dengan: document.getElementById('dlg-laporan').showModal()  /  'dlg-ulasan'
--}}
<dialog id="dlg-laporan" class="w-full max-w-md rounded-2xl border border-slate-200 p-0 shadow-xl backdrop:bg-slate-900/50">
    <form method="POST" action="{{ route('laporan.store', $project) }}" class="space-y-4 p-6">
        @csrf
        <h3 class="text-lg font-bold text-slate-900">Laporkan</h3>
        <p class="text-xs text-slate-500">Laporan dikirim ke admin untuk ditinjau.</p>

        <div>
            <label for="fb-report-target" class="mb-1 block text-sm font-semibold text-slate-800">Yang dilaporkan</label>
            <select id="fb-report-target" name="reported_user_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Proyek ini secara keseluruhan</option>
                @foreach ($others as $other)
                    <option value="{{ $other->id }}">{{ $other->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="fb-report-content" class="mb-1 block text-sm font-semibold text-slate-800">Isi laporan</label>
            <textarea id="fb-report-content" name="content" rows="4" required minlength="10" maxlength="2000" placeholder="Jelaskan masalahnya (minimal 10 karakter)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" onclick="this.closest('dialog').close()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
            <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Kirim laporan</button>
        </div>
    </form>
</dialog>

<dialog id="dlg-ulasan" class="w-full max-w-md rounded-2xl border border-slate-200 p-0 shadow-xl backdrop:bg-slate-900/50">
    <form method="POST" action="{{ route('ulasan.store', $project) }}" class="space-y-4 p-6">
        @csrf
        <h3 class="text-lg font-bold text-slate-900">Beri ulasan</h3>

        @if ($others->isEmpty())
            <p class="text-sm text-slate-500">Belum ada peserta lain di proyek ini yang bisa diulas.</p>
            <div class="flex justify-end">
                <button type="button" onclick="this.closest('dialog').close()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Tutup</button>
            </div>
        @else
            <div>
                <label for="fb-review-target" class="mb-1 block text-sm font-semibold text-slate-800">Ulas siapa</label>
                <select id="fb-review-target" name="reviewee_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach ($others as $other)
                        <option value="{{ $other->id }}">{{ $other->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="fb-review-rating" class="mb-1 block text-sm font-semibold text-slate-800">Rating</label>
                <select id="fb-review-rating" name="rating" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="5">5 - Sangat baik</option>
                    <option value="4">4 - Baik</option>
                    <option value="3">3 - Cukup</option>
                    <option value="2">2 - Kurang</option>
                    <option value="1">1 - Buruk</option>
                </select>
            </div>

            <div>
                <label for="fb-review-comment" class="mb-1 block text-sm font-semibold text-slate-800">Komentar (opsional)</label>
                <textarea id="fb-review-comment" name="comment" rows="3" maxlength="1000" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="this.closest('dialog').close()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Kirim ulasan</button>
            </div>
        @endif
    </form>
</dialog>
