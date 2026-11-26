<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Report;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Laporan (report) dan ulasan (review) antar peserta proyek.
 *
 * Peserta proyek = owner + semua anggota tim. Controller ini dipakai bersama
 * oleh Mahasiswa dan Project Owner, jadi otorisasinya berdasarkan keanggotaan
 * proyek, bukan berdasarkan role.
 */
class TeamFeedbackController extends Controller
{
    public function storeLaporan(Request $request, Project $project): RedirectResponse
    {
        $participants = $this->participants($project);
        $me = Auth::id();

        abort_unless($participants->has($me), 403, 'Kamu bukan peserta proyek ini.');

        $validator = Validator::make($request->all(), [
            'reported_user_id' => ['nullable', 'integer'],
            'content' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'content.required' => 'Isi laporan wajib diisi.',
            'content.min' => 'Isi laporan minimal 10 karakter.',
            'content.max' => 'Isi laporan maksimal 2000 karakter.',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $targetId = $request->filled('reported_user_id') ? (int) $request->input('reported_user_id') : null;

        if ($targetId !== null && ($targetId === $me || ! $participants->has($targetId))) {
            return back()->with('error', 'Pengguna yang dilaporkan harus peserta proyek ini dan bukan dirimu sendiri.');
        }

        // Skema tabel reports (user_id, content, status) sesuai ERD laporan tidak diubah,
        // jadi konteks laporan dicatat di awal isi supaya admin tahu proyek dan pihak yang dilaporkan.
        $target = $targetId !== null ? $participants->get($targetId) : null;
        $header = [
            'Proyek: '.$project->name,
            $target
                ? 'Dilaporkan: '.$target->name.' ('.($target->id === $project->owner_id ? 'Project Owner' : 'Anggota tim').')'
                : 'Dilaporkan: proyek secara keseluruhan',
        ];

        Report::create([
            'user_id' => $me,
            'content' => implode("\n", $header)."\n\n".$validator->validated()['content'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Laporan terkirim ke admin dan akan ditinjau.');
    }

    public function storeUlasan(Request $request, Project $project): RedirectResponse
    {
        $participants = $this->participants($project);
        $me = Auth::id();

        abort_unless($participants->has($me), 403, 'Kamu bukan peserta proyek ini.');

        $validator = Validator::make($request->all(), [
            'reviewee_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'reviewee_id.required' => 'Pilih siapa yang ingin kamu ulas.',
            'rating.required' => 'Pilih rating 1 sampai 5.',
            'rating.between' => 'Rating harus antara 1 sampai 5.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $data = $validator->validated();
        $revieweeId = (int) $data['reviewee_id'];

        if ($revieweeId === $me || ! $participants->has($revieweeId)) {
            return back()->with('error', 'Kamu hanya bisa mengulas peserta lain di proyek ini.');
        }

        // Satu ulasan per pasangan (proyek, pemberi, penerima). Mengulas lagi memperbarui ulasan lama.
        $review = Review::updateOrCreate(
            ['project_id' => $project->id, 'reviewer_id' => $me, 'reviewee_id' => $revieweeId],
            ['rating' => $data['rating'], 'comment' => $data['comment'] ?? null],
        );

        return back()->with('success', $review->wasRecentlyCreated
            ? 'Ulasan terkirim. Terima kasih!'
            : 'Ulasanmu sebelumnya diperbarui.');
    }

    /**
     * Owner + anggota tim, dikunci dengan id pengguna.
     *
     * @return Collection<int, \App\Models\User>
     */
    private function participants(Project $project): Collection
    {
        $project->loadMissing(['owner', 'teamMembers.user']);

        return collect([$project->owner])
            ->merge($project->teamMembers->pluck('user'))
            ->filter()
            ->keyBy('id');
    }
}
