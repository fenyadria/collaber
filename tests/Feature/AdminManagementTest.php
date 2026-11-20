<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_users_and_view_user_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create([
            'name' => 'Maya Mahasiswa',
            'email' => 'maya@example.test',
            'role' => 'mahasiswa',
        ]);
        User::factory()->create([
            'name' => 'Owner Lain',
            'email' => 'owner@example.test',
            'role' => 'project_owner',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pengguna', ['q' => 'maya']))
            ->assertOk()
            ->assertSee('Maya Mahasiswa')
            ->assertDontSee('Owner Lain');

        $this->actingAs($admin)
            ->get(route('admin.pengguna.show', $member))
            ->assertOk()
            ->assertSee('maya@example.test')
            ->assertSee('Mahasiswa');
    }

    public function test_admin_can_deactivate_and_reactivate_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($admin)
            ->post(route('admin.pengguna.status', $member), ['status' => 'inactive'])
            ->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $member->id, 'status' => 'inactive']);

        $this->post(route('admin.pengguna.status', $member), ['status' => 'active'])
            ->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $member->id, 'status' => 'active']);
    }

    public function test_admin_cannot_deactivate_the_current_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.pengguna.status', $admin), ['status' => 'inactive'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'status' => 'active']);
    }

    public function test_admin_can_filter_and_update_report_follow_up_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reporter = User::factory()->create(['role' => 'mahasiswa']);
        $reportId = DB::table('reports')->insertGetId([
            'user_id' => $reporter->id,
            'content' => 'Tidak bertanggung jawab pada proyek.',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.laporan', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('Tidak bertanggung jawab pada proyek.')
            ->assertSee('Lihat detail laporan');

        $this->post(route('admin.laporan.status', $reportId), ['status' => 'in_review'])
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $reportId,
            'status' => 'in_review',
        ]);
    }

    public function test_admin_dashboard_uses_database_report_counts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reporter = User::factory()->create(['role' => 'mahasiswa']);
        DB::table('reports')->insert([
            'user_id' => $reporter->id,
            'content' => 'Laporan terbaru dari database.',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Laporan terbaru dari database.')
            ->assertSee('1 baru');
    }

    public function test_admin_can_verify_a_pending_project(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'project_owner']);
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Aplikasi Web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $projectId = DB::table('projects')->insertGetId([
            'owner_id' => $owner->id,
            'category_id' => $categoryId,
            'name' => 'Proyek Uji',
            'description' => 'Proyek untuk pengujian verifikasi.',
            'max_members' => 4,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.verifikasi-proyek'))
            ->assertOk()
            ->assertSee('Proyek Uji')
            ->assertSee('Approve');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Proyek Uji')
            ->assertSee($owner->name);

        $this->post(route('admin.verifikasi-proyek.status', $projectId), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'id' => $projectId,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_manage_catalog_items_and_cannot_delete_items_in_use(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.catalog.store', 'skill'), ['name' => 'Laravel'])
            ->assertRedirect(route('admin.kategori-skill'));

        $skillId = DB::table('skills')->where('name', 'Laravel')->value('id');

        $this->put(route('admin.catalog.update', ['skill', $skillId]), ['name' => 'PHP'])
            ->assertRedirect(route('admin.kategori-skill'));
        $this->assertDatabaseHas('skills', ['id' => $skillId, 'name' => 'PHP']);

        $this->get(route('admin.kategori-skill'))
            ->assertOk()
            ->assertSee('PHP')
            ->assertSee('+ Tambah');

        $user = User::factory()->create(['role' => 'mahasiswa']);
        DB::table('user_skills')->insert([
            'user_id' => $user->id,
            'skill_id' => $skillId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->delete(route('admin.catalog.delete', ['skill', $skillId]))
            ->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('skills', ['id' => $skillId]);

        DB::table('user_skills')->where('skill_id', $skillId)->delete();
        $this->delete(route('admin.catalog.delete', ['skill', $skillId]))
            ->assertRedirect(route('admin.kategori-skill'));
        $this->assertDatabaseMissing('skills', ['id' => $skillId]);
    }

    public function test_non_admin_cannot_manage_admin_pages(): void
    {
        $member = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($member)
            ->get(route('admin.pengguna'))
            ->assertForbidden();
    }
}
