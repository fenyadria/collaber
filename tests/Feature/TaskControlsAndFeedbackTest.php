<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Report;
use App\Models\Review;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskControlsAndFeedbackTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $member;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => 'project_owner', 'name' => 'Nana Owner']);
        $this->member = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Maya Anggota']);

        $this->project = Project::create([
            'owner_id' => $this->owner->id,
            'category_id' => Category::create(['name' => 'Aplikasi Web'])->id,
            'name' => 'Voca',
            'description' => 'Deskripsi',
            'max_members' => 3,
            'status' => 'approved',
        ]);

        TeamMember::create(['project_id' => $this->project->id, 'user_id' => $this->member->id, 'joined_at' => now()]);
    }

    private function task(array $attributes = []): ProjectTask
    {
        return ProjectTask::create(array_merge(['project_id' => $this->project->id, 'title' => 'Buat ERD'], $attributes));
    }

    // ---- Owner menangani tugas ----

    public function test_owner_can_change_task_status_and_only_valid_values(): void
    {
        $task = $this->task();

        $this->actingAs($this->owner)->patch(route('project-owner.workspace.tasks.status', $task), ['status' => 'done'])
            ->assertRedirect(route('project-owner.workspace', $this->project));
        $this->assertSame('done', $task->fresh()->status);

        $this->actingAs($this->owner)->patch(route('project-owner.workspace.tasks.status', $task), ['status' => 'ngawur'])
            ->assertSessionHasErrors('status');
        $this->assertSame('done', $task->fresh()->status);

        $this->actingAs(User::factory()->create(['role' => 'project_owner']))
            ->patch(route('project-owner.workspace.tasks.status', $task), ['status' => 'todo'])->assertForbidden();
    }

    public function test_owner_can_edit_task_and_reassign_only_to_team_members(): void
    {
        $task = $this->task();
        $outsider = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($this->owner)->put(route('project-owner.workspace.tasks.update', $task), [
            'title' => 'ERD final', 'description' => 'Revisi', 'assignees' => [$outsider->id],
        ])->assertSessionHasErrors('assignees.0');

        $this->actingAs($this->owner)->put(route('project-owner.workspace.tasks.update', $task), [
            'title' => 'ERD final', 'description' => 'Revisi', 'deadline' => '2030-01-31', 'assignees' => [$this->member->id],
        ])->assertRedirect(route('project-owner.workspace', $this->project));

        $task->refresh();
        $this->assertSame('ERD final', $task->title);
        $this->assertTrue($task->assignees->contains($this->member));

        $this->actingAs($this->owner)->put(route('project-owner.workspace.tasks.update', $task), ['title' => ''])
            ->assertSessionHasErrors('title');
    }

    public function test_owner_can_upload_replace_and_delete_submission_file(): void
    {
        Storage::fake('public');
        $task = $this->task();

        $this->actingAs($this->owner)->post(route('project-owner.workspace.tasks.submission', $task), [
            'submission' => UploadedFile::fake()->create('erd.pdf', 100, 'application/pdf'),
            'submission_note' => 'Link drive',
        ])->assertRedirect(route('project-owner.workspace', $this->project));

        $first = $task->fresh()->submission_path;
        Storage::disk('public')->assertExists($first);

        $this->actingAs($this->owner)->get(route('project-owner.workspace', $this->project))
            ->assertOk()->assertSee('Hasil kerja diunggah')->assertSee('Link drive')->assertSee('Lihat file');

        $this->actingAs($this->owner)->post(route('project-owner.workspace.tasks.submission', $task), [
            'submission' => UploadedFile::fake()->create('erd2.pdf', 100, 'application/pdf'),
        ]);
        $second = $task->fresh()->submission_path;
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);

        $this->actingAs($this->owner)->delete(route('project-owner.workspace.tasks.destroy', $task));
        Storage::disk('public')->assertMissing($second);
    }

    public function test_submission_rejects_wrong_file_type_and_other_owners(): void
    {
        Storage::fake('public');
        $task = $this->task();

        $this->actingAs($this->owner)->post(route('project-owner.workspace.tasks.submission', $task), [
            'submission' => UploadedFile::fake()->create('virus.exe', 10, 'application/octet-stream'),
        ])->assertSessionHasErrors('submission');

        $this->actingAs(User::factory()->create(['role' => 'project_owner']))
            ->post(route('project-owner.workspace.tasks.submission', $task), [
                'submission' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf'),
            ])->assertForbidden();
    }

    public function test_workspace_shows_submission_from_a_student(): void
    {
        $this->task(['status' => 'done', 'submission_path' => 'task-submissions/x.pdf', 'submission_note' => 'Dari Maya']);

        $this->actingAs($this->owner)->get(route('project-owner.workspace', $this->project))
            ->assertOk()->assertSee('Dari Maya')->assertSee('storage/task-submissions/x.pdf');
    }

    // ---- Laporan ----

    public function test_member_can_report_project_and_context_is_saved_for_admin(): void
    {
        $this->actingAs($this->member)->post(route('laporan.store', $this->project), [
            'content' => 'Proyek ini terindikasi spam dan tidak jelas.',
        ])->assertSessionHas('success');

        $report = Report::first();
        $this->assertSame($this->member->id, $report->user_id);
        $this->assertSame('pending', $report->status);
        $this->assertStringContainsString('Proyek: Voca', $report->content);
        $this->assertStringContainsString('Dilaporkan: proyek secara keseluruhan', $report->content);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.laporan'))
            ->assertOk()->assertSee('Proyek: Voca')->assertSee('terindikasi spam');
    }

    public function test_owner_and_member_can_report_each_other(): void
    {
        $this->actingAs($this->owner)->post(route('laporan.store', $this->project), [
            'reported_user_id' => $this->member->id, 'content' => 'Anggota tidak bertanggung jawab.',
        ])->assertSessionHas('success');

        $this->actingAs($this->member)->post(route('laporan.store', $this->project), [
            'reported_user_id' => $this->owner->id, 'content' => 'Owner tidak responsif sama sekali.',
        ])->assertSessionHas('success');

        $this->assertSame(2, Report::count());
        $this->assertStringContainsString('Dilaporkan: Maya Anggota (Anggota tim)', Report::orderBy('id')->first()->content);
        $this->assertStringContainsString('Dilaporkan: Nana Owner (Project Owner)', Report::orderByDesc('id')->first()->content);
    }

    public function test_report_validation_and_authorization(): void
    {
        $outsider = User::factory()->create(['role' => 'mahasiswa']);

        // Bukan peserta proyek.
        $this->actingAs($outsider)->post(route('laporan.store', $this->project), ['content' => 'Isi laporan panjang.'])->assertForbidden();

        // Tidak boleh melaporkan diri sendiri / orang di luar proyek / isi terlalu pendek.
        $this->actingAs($this->member)->post(route('laporan.store', $this->project), ['reported_user_id' => $this->member->id, 'content' => 'Isi laporan panjang.'])->assertSessionHas('error');
        $this->actingAs($this->member)->post(route('laporan.store', $this->project), ['reported_user_id' => $outsider->id, 'content' => 'Isi laporan panjang.'])->assertSessionHas('error');
        $this->actingAs($this->member)->post(route('laporan.store', $this->project), ['content' => 'pendek'])->assertSessionHas('error');

        $this->assertSame(0, Report::count());
        auth()->logout();
        $this->post(route('laporan.store', $this->project), ['content' => 'Isi laporan panjang.'])->assertRedirect('/login');
    }

    // ---- Ulasan ----

    public function test_member_can_review_owner_and_review_is_updated_not_duplicated(): void
    {
        $this->actingAs($this->member)->post(route('ulasan.store', $this->project), [
            'reviewee_id' => $this->owner->id, 'rating' => 4, 'comment' => 'Komunikatif',
        ])->assertSessionHas('success');

        $this->actingAs($this->member)->post(route('ulasan.store', $this->project), [
            'reviewee_id' => $this->owner->id, 'rating' => 2,
        ])->assertSessionHas('success');

        $this->assertSame(1, Review::count());
        $this->assertSame(2, Review::first()->rating);
    }

    public function test_review_validation_and_authorization(): void
    {
        $outsider = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($outsider)->post(route('ulasan.store', $this->project), ['reviewee_id' => $this->owner->id, 'rating' => 5])->assertForbidden();
        $this->actingAs($this->member)->post(route('ulasan.store', $this->project), ['reviewee_id' => $this->member->id, 'rating' => 5])->assertSessionHas('error');
        $this->actingAs($this->member)->post(route('ulasan.store', $this->project), ['reviewee_id' => $outsider->id, 'rating' => 5])->assertSessionHas('error');
        $this->actingAs($this->member)->post(route('ulasan.store', $this->project), ['reviewee_id' => $this->owner->id, 'rating' => 9])->assertSessionHas('error');
        $this->actingAs($this->member)->post(route('ulasan.store', $this->project), ['rating' => 3])->assertSessionHas('error');

        $this->assertSame(0, Review::count());
    }

    public function test_owner_sees_ratings_on_workspace_and_applicant_cards(): void
    {
        Review::create(['project_id' => $this->project->id, 'reviewer_id' => $this->owner->id, 'reviewee_id' => $this->member->id, 'rating' => 5]);
        Review::create(['project_id' => $this->project->id, 'reviewer_id' => User::factory()->create()->id, 'reviewee_id' => $this->member->id, 'rating' => 4]);

        $this->actingAs($this->owner)->get(route('project-owner.workspace', $this->project))
            ->assertOk()->assertSee('4.5')->assertSee('2 ulasan');

        \App\Models\Application::create(['project_id' => $this->project->id, 'user_id' => $this->member->id, 'status' => 'pending']);
        $this->actingAs($this->owner)->get(route('project-owner.pelamar'))
            ->assertOk()->assertSee('4.5')->assertSee('2 ulasan');
    }

    // ---- Tampilan ----

    public function test_buttons_and_dialogs_render_for_owner_and_student(): void
    {
        $this->actingAs($this->owner)->get(route('project-owner.workspace', $this->project))
            ->assertOk()->assertSee('dlg-laporan', false)->assertSee('dlg-ulasan', false)->assertSee('Maya Anggota');

        $this->actingAs($this->member)->get(route('mahasiswa.tim-saya'))
            ->assertOk()->assertSee('dlg-laporan', false)->assertSee('dlg-ulasan', false)->assertSee('Nana Owner');
    }
}
