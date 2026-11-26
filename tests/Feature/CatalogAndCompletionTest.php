<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Review;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogAndCompletionTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create(['role' => 'project_owner']);
    }

    private function student(bool $withCv = true): User
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        if ($withCv) {
            Profile::create(['user_id' => $student->id, 'cv_path' => 'cvs/cv.pdf']);
        }

        return $student;
    }

    private function project(User $owner, string $name, string $status, int $max = 3): Project
    {
        return Project::create([
            'owner_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['name' => 'Aplikasi Web'])->id,
            'name' => $name,
            'description' => 'Deskripsi',
            'max_members' => $max,
            'status' => $status,
        ]);
    }

    // ---- Nomor 1: katalog hanya proyek approved ----

    public function test_catalog_and_dashboard_only_show_approved_projects(): void
    {
        $owner = $this->owner();
        foreach (['draft', 'pending', 'rejected', 'completed'] as $status) {
            $this->project($owner, "Proyek-{$status}", $status);
        }
        $this->project($owner, 'Proyek-approved', 'approved');
        $student = $this->student();

        $response = $this->actingAs($student)->get(route('mahasiswa.cari-proyek'))->assertOk();
        $response->assertSee('Proyek-approved');
        foreach (['draft', 'pending', 'rejected', 'completed'] as $status) {
            $response->assertDontSee("Proyek-{$status}");
        }

        $dashboard = $this->actingAs($student)->get(route('mahasiswa.dashboard'))->assertOk();
        $dashboard->assertSee('Proyek-approved');
        $dashboard->assertDontSee('Proyek-pending');
        $dashboard->assertDontSee('Proyek-draft');
    }

    public function test_student_cannot_apply_to_unapproved_project_but_can_to_approved(): void
    {
        $owner = $this->owner();
        $pending = $this->project($owner, 'Masih pending', 'pending');
        $approved = $this->project($owner, 'Sudah approved', 'approved');
        $student = $this->student();

        $this->actingAs($student)->post(route('mahasiswa.cari-proyek.apply', $pending), ['motivation' => 'Mau ikut'])
            ->assertSessionHas('error');
        $this->assertDatabaseMissing('applications', ['project_id' => $pending->id]);

        $this->actingAs($student)->post(route('mahasiswa.cari-proyek.apply', $approved), ['motivation' => 'Mau ikut'])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('applications', ['project_id' => $approved->id, 'user_id' => $student->id, 'status' => 'pending']);
    }

    // ---- Nomor 2: tandai proyek selesai ----

    public function test_owner_can_complete_approved_project_and_pending_applications_are_rejected(): void
    {
        $owner = $this->owner();
        $project = $this->project($owner, 'Voca', 'approved');
        $member = $this->student();
        $waiting = $this->student();
        TeamMember::create(['project_id' => $project->id, 'user_id' => $member->id, 'joined_at' => now()]);
        $accepted = Application::create(['project_id' => $project->id, 'user_id' => $member->id, 'status' => 'accepted']);
        $pending = Application::create(['project_id' => $project->id, 'user_id' => $waiting->id, 'status' => 'pending']);

        $this->actingAs($owner)->post(route('project-owner.workspace.complete', $project))
            ->assertRedirect(route('project-owner.workspace', $project))->assertSessionHas('success');

        $this->assertSame('completed', $project->fresh()->status);
        $this->assertSame('rejected', $pending->fresh()->status);
        $this->assertNotNull($pending->fresh()->responded_at);
        $this->assertSame('accepted', $accepted->fresh()->status);
    }

    public function test_only_the_owner_can_complete_and_only_approved_projects(): void
    {
        $owner = $this->owner();
        $approved = $this->project($owner, 'Voca', 'approved');
        $pending = $this->project($owner, 'Belum jalan', 'pending');

        $this->actingAs($this->owner())->post(route('project-owner.workspace.complete', $approved))->assertForbidden();
        $this->actingAs($this->student())->post(route('project-owner.workspace.complete', $approved))->assertForbidden();

        $this->actingAs($owner)->post(route('project-owner.workspace.complete', $pending))->assertSessionHas('error');
        $this->assertSame('pending', $pending->fresh()->status);

        // Dua kali menandai selesai tidak merusak apa pun.
        $this->actingAs($owner)->post(route('project-owner.workspace.complete', $approved));
        $this->actingAs($owner)->post(route('project-owner.workspace.complete', $approved))->assertSessionHas('error');
        $this->assertSame('completed', $approved->fresh()->status);
    }

    public function test_completed_project_workspace_is_locked_but_reviews_and_reports_still_work(): void
    {
        $owner = $this->owner();
        $project = $this->project($owner, 'Voca', 'completed');
        $member = $this->student();
        TeamMember::create(['project_id' => $project->id, 'user_id' => $member->id, 'joined_at' => now()]);
        $task = $project->tasks()->create(['title' => 'Buat ERD']);

        $page = $this->actingAs($owner)->get(route('project-owner.workspace', $project))->assertOk();
        $page->assertSee('sudah')->assertSee('selesai')->assertSee('Buat ERD');
        $page->assertDontSee('Tandai proyek selesai')->assertDontSee('Tambah tugas')->assertDontSee('Edit tugas');

        // Tugas dikunci.
        $this->actingAs($owner)->patch(route('project-owner.workspace.tasks.status', $task), ['status' => 'done'])->assertSessionHas('error');
        $this->actingAs($owner)->put(route('project-owner.workspace.tasks.update', $task), ['title' => 'Ubah'])->assertSessionHas('error');
        $this->actingAs($owner)->post(route('project-owner.workspace.tasks.store', $project), ['title' => 'Baru'])->assertSessionHas('error');
        $this->actingAs($owner)->delete(route('project-owner.workspace.tasks.destroy', $task))->assertSessionHas('error');
        $this->assertSame('todo', $task->fresh()->status);
        $this->assertSame(1, $project->tasks()->count());

        // Ulasan dan laporan tetap bisa setelah selesai.
        $this->actingAs($owner)->post(route('ulasan.store', $project), ['reviewee_id' => $member->id, 'rating' => 5])->assertSessionHas('success');
        $this->actingAs($member)->post(route('ulasan.store', $project), ['reviewee_id' => $owner->id, 'rating' => 4])->assertSessionHas('success');
        $this->assertSame(2, Review::count());
    }

    public function test_completed_project_appears_in_my_projects_with_workspace_link_and_filter(): void
    {
        $owner = $this->owner();
        $project = $this->project($owner, 'Voca', 'completed');

        $this->actingAs($owner)->get(route('project-owner.proyek-saya'))
            ->assertOk()->assertSee('completed')->assertSee(route('project-owner.workspace', $project), false)
            ->assertDontSee(route('project-owner.proyek.edit', $project), false);

        $this->actingAs($owner)->get(route('project-owner.proyek-saya', ['status' => 'completed']))
            ->assertOk()->assertSee('Voca');

        // Proyek selesai tidak bisa diedit atau dihapus.
        $this->actingAs($owner)->delete(route('project-owner.proyek.destroy', $project))->assertSessionHas('error');
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_after_completion_member_is_free_to_join_another_project(): void
    {
        $owner = $this->owner();
        $first = $this->project($owner, 'Proyek A', 'approved');
        $second = $this->project($owner, 'Proyek B', 'approved');
        $student = $this->student();
        TeamMember::create(['project_id' => $first->id, 'user_id' => $student->id, 'joined_at' => now()]);

        // Selama proyek A berjalan, tidak boleh melamar proyek lain.
        $this->actingAs($student)->post(route('mahasiswa.cari-proyek.apply', $second), ['motivation' => 'Mau ikut'])
            ->assertSessionHas('error');
        $this->assertDatabaseMissing('applications', ['project_id' => $second->id]);

        $this->actingAs($owner)->post(route('project-owner.workspace.complete', $first));

        $this->actingAs($student)->post(route('mahasiswa.cari-proyek.apply', $second), ['motivation' => 'Mau ikut'])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('applications', ['project_id' => $second->id, 'user_id' => $student->id]);

        // Mahasiswa yang proyeknya selesai masih melihat tim lamanya di Tim Saya (untuk ulasan).
        $this->actingAs($student)->get(route('mahasiswa.tim-saya'))->assertOk()->assertSee('Proyek A')->assertSee('dlg-ulasan', false);
    }
}
