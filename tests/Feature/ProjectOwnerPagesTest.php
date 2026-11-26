<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectOwnerPagesTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create(['role' => 'project_owner']);
    }

    private function student(string $name = 'Mahasiswa'): User
    {
        return User::factory()->create(['role' => 'mahasiswa', 'name' => $name]);
    }

    private function project(User $owner, array $attributes = []): Project
    {
        $category = Category::firstOrCreate(['name' => 'Aplikasi Web']);

        return Project::create(array_merge([
            'owner_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Aplikasi Kasir UMKM',
            'description' => 'Deskripsi',
            'max_members' => 2,
            'status' => 'approved',
        ], $attributes));
    }

    public function test_owner_can_open_all_owner_pages(): void
    {
        $owner = $this->owner();
        $this->project($owner);

        $this->actingAs($owner)->get(route('project-owner.dashboard'))
            ->assertOk()->assertSee('Dashboard Project Owner')->assertSee('Lamaran masuk')->assertSee('Pelamar terbaru');
        $this->actingAs($owner)->get(route('project-owner.proyek-saya'))
            ->assertOk()->assertSee('Aplikasi Kasir UMKM');
        $this->actingAs($owner)->get(route('project-owner.posting-proyek'))
            ->assertOk()->assertSee('Alur verifikasi')->assertSee('Kirim untuk verifikasi');
        $this->actingAs($owner)->get(route('project-owner.pelamar'))->assertOk()->assertSee('Pelamar');
        $this->actingAs($owner)->get(route('project-owner.workspace'))->assertOk()->assertSee('Papan tugas');
    }

    public function test_workspace_without_approved_project_shows_empty_state(): void
    {
        $this->actingAs($this->owner())->get(route('project-owner.workspace'))
            ->assertOk()->assertSee('Belum ada proyek yang disetujui');
    }

    public function test_proyek_saya_only_lists_own_projects_and_can_be_filtered(): void
    {
        $owner = $this->owner();
        $this->project($owner, ['name' => 'Sistem Informasi Perpustakaan', 'status' => 'pending']);
        $this->project($owner, ['name' => 'Poster Kampanye', 'status' => 'rejected']);
        $this->project($this->owner(), ['name' => 'Proyek Orang Lain']);

        $this->actingAs($owner)->get(route('project-owner.proyek-saya'))
            ->assertOk()->assertDontSee('Proyek Orang Lain')->assertSee('Lihat alasan');

        $this->actingAs($owner)->get(route('project-owner.proyek-saya', ['status' => 'pending']))
            ->assertOk()->assertSee('Sistem Informasi Perpustakaan')->assertDontSee('Poster Kampanye');
    }

    public function test_posting_proyek_validates_input(): void
    {
        $this->actingAs($this->owner())->post(route('project-owner.posting-proyek.store'), ['action' => 'submit'])
            ->assertSessionHasErrors(['name', 'description', 'category_id', 'max_members']);
    }

    public function test_owner_can_save_draft_and_submit_project_with_skills(): void
    {
        $owner = $this->owner();
        $category = Category::create(['name' => 'Desain']);
        Skill::create(['name' => 'Figma']);

        $payload = [
            'name' => 'Aplikasi Kasir', 'description' => 'Deskripsi', 'category_id' => $category->id,
            'max_members' => 4, 'skills' => ['Figma', 'Skill Baru'],
        ];

        $this->actingAs($owner)->post(route('project-owner.posting-proyek.store'), $payload + ['action' => 'draft'])
            ->assertRedirect(route('project-owner.proyek-saya'));
        $this->actingAs($owner)->post(route('project-owner.posting-proyek.store'), $payload + ['action' => 'submit'])
            ->assertRedirect(route('project-owner.proyek-saya'));

        $this->assertDatabaseHas('projects', ['owner_id' => $owner->id, 'status' => 'draft']);
        $this->assertDatabaseHas('projects', ['owner_id' => $owner->id, 'status' => 'pending']);
        $this->assertDatabaseHas('skills', ['name' => 'Skill Baru']);
        $this->assertSame(2, Project::where('status', 'pending')->first()->skills()->count());
    }

    public function test_owner_can_edit_and_resubmit_rejected_project(): void
    {
        $owner = $this->owner();
        $project = $this->project($owner, ['status' => 'rejected']);

        $this->actingAs($owner)->get(route('project-owner.proyek.edit', $project))
            ->assertOk()->assertSee('Kirim ulang untuk verifikasi');

        $this->actingAs($owner)->put(route('project-owner.proyek.update', $project), [
            'name' => 'Revisi', 'description' => 'Lebih jelas', 'category_id' => $project->category_id,
            'max_members' => 3, 'action' => 'submit',
        ])->assertRedirect(route('project-owner.proyek-saya'));

        $project->refresh();
        $this->assertSame('pending', $project->status);
        $this->assertSame('Revisi', $project->name);
    }

    public function test_approved_project_cannot_be_edited_or_deleted_and_others_cannot_touch_it(): void
    {
        $owner = $this->owner();
        $approved = $this->project($owner);
        $draft = $this->project($owner, ['status' => 'draft']);

        $this->actingAs($owner)->get(route('project-owner.proyek.edit', $approved))
            ->assertRedirect(route('project-owner.proyek-saya'));
        $this->actingAs($owner)->delete(route('project-owner.proyek.destroy', $approved))->assertSessionHas('error');
        $this->assertDatabaseHas('projects', ['id' => $approved->id]);

        $this->actingAs($this->owner())->get(route('project-owner.proyek.edit', $draft))->assertForbidden();
        $this->actingAs($this->owner())->delete(route('project-owner.proyek.destroy', $draft))->assertForbidden();

        $this->actingAs($owner)->delete(route('project-owner.proyek.destroy', $draft))
            ->assertRedirect(route('project-owner.proyek-saya'));
        $this->assertDatabaseMissing('projects', ['id' => $draft->id]);
    }

    public function test_owner_can_accept_applicant_and_member_is_created(): void
    {
        $owner = $this->owner();
        $project = $this->project($owner);
        $student = $this->student('Maya');
        $application = Application::create(['project_id' => $project->id, 'user_id' => $student->id, 'message' => 'Halo', 'status' => 'pending']);

        $this->actingAs($owner)->get(route('project-owner.pelamar'))->assertOk()->assertSee('Maya');

        $this->actingAs($owner)->post(route('project-owner.pelamar.status', $application), ['status' => 'accepted'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('team_members', ['project_id' => $project->id, 'user_id' => $student->id]);
        $application->refresh();
        $this->assertSame('accepted', $application->status);
        $this->assertNotNull($application->responded_at);

        // Sudah diproses, tidak bisa diproses ulang.
        $this->actingAs($owner)->post(route('project-owner.pelamar.status', $application), ['status' => 'rejected'])
            ->assertSessionHas('error');
        $this->assertSame(1, TeamMember::count());
    }

    public function test_owner_can_reject_applicant_without_creating_member(): void
    {
        $owner = $this->owner();
        $project = $this->project($owner);
        $application = Application::create(['project_id' => $project->id, 'user_id' => $this->student()->id, 'status' => 'pending']);

        $this->actingAs($owner)->post(route('project-owner.pelamar.status', $application), ['status' => 'rejected'])
            ->assertSessionHas('success');

        $this->assertSame('rejected', $application->fresh()->status);
        $this->assertSame(0, TeamMember::count());
    }

    public function test_accept_is_blocked_when_quota_full_or_student_in_other_project(): void
    {
        $owner = $this->owner();
        $project = $this->project($owner, ['max_members' => 1]);
        TeamMember::create(['project_id' => $project->id, 'user_id' => $this->student()->id, 'joined_at' => now()]);
        $late = Application::create(['project_id' => $project->id, 'user_id' => $this->student()->id, 'status' => 'pending']);

        $this->actingAs($owner)->post(route('project-owner.pelamar.status', $late), ['status' => 'accepted'])
            ->assertSessionHas('error');
        $this->assertSame('pending', $late->fresh()->status);

        $busy = $this->student();
        $otherProject = $this->project($this->owner(), ['name' => 'Lain']);
        TeamMember::create(['project_id' => $otherProject->id, 'user_id' => $busy->id, 'joined_at' => now()]);
        $roomy = $this->project($owner, ['name' => 'Longgar', 'max_members' => 5]);
        $application = Application::create(['project_id' => $roomy->id, 'user_id' => $busy->id, 'status' => 'pending']);

        $this->actingAs($owner)->post(route('project-owner.pelamar.status', $application), ['status' => 'accepted'])
            ->assertSessionHas('error');
        $this->assertSame('pending', $application->fresh()->status);
    }

    public function test_owner_cannot_process_applicant_of_another_owners_project(): void
    {
        $project = $this->project($this->owner());
        $application = Application::create(['project_id' => $project->id, 'user_id' => $this->student()->id, 'status' => 'pending']);

        $this->actingAs($this->owner())->post(route('project-owner.pelamar.status', $application), ['status' => 'accepted'])
            ->assertForbidden();
    }

    public function test_owner_can_create_task_for_team_members_only_and_delete_it(): void
    {
        $owner = $this->owner();
        $project = $this->project($owner);
        $member = $this->student('Anggota');
        $outsider = $this->student('Orang Luar');
        TeamMember::create(['project_id' => $project->id, 'user_id' => $member->id, 'joined_at' => now()]);

        $this->actingAs($owner)->post(route('project-owner.workspace.tasks.store', $project), [
            'title' => 'Buat ERD', 'assignees' => [$outsider->id],
        ])->assertSessionHasErrors('assignees.0');

        $this->actingAs($owner)->post(route('project-owner.workspace.tasks.store', $project), [
            'title' => 'Buat ERD', 'assignees' => [$member->id],
        ])->assertRedirect(route('project-owner.workspace', $project));

        $task = $project->tasks()->first();
        $this->assertSame('todo', $task->status);
        $this->assertTrue($task->assignees->contains($member));

        $this->actingAs($owner)->get(route('project-owner.workspace', $project))->assertOk()->assertSee('Buat ERD')->assertSee('Anggota');

        $this->actingAs($this->owner())->delete(route('project-owner.workspace.tasks.destroy', $task))->assertForbidden();
        $this->actingAs($owner)->delete(route('project-owner.workspace.tasks.destroy', $task));
        $this->assertDatabaseMissing('project_tasks', ['id' => $task->id]);
    }

    public function test_non_owner_is_redirected_or_forbidden(): void
    {
        $this->get(route('project-owner.dashboard'))->assertRedirect('/login');

        $this->actingAs($this->student())->get(route('project-owner.posting-proyek'))->assertForbidden();
    }
}
