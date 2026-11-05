<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProjectOwnerPagesTest extends TestCase
{
    private function owner(): array
    {
        return ['user' => ['name' => 'Project Owner', 'role' => 'project_owner']];
    }

    public function test_owner_can_open_all_owner_pages(): void
    {
        $this->withSession($this->owner())->get(route('project-owner.dashboard'))
            ->assertOk()->assertSee('Dashboard Project Owner')->assertSee('Lamaran masuk')->assertSee('Pelamar terbaru');

        $this->withSession($this->owner())->get(route('project-owner.proyek-saya'))
            ->assertOk()->assertSee('Aplikasi Kasir UMKM')->assertSee('Lihat alasan');

        $this->withSession($this->owner())->get(route('project-owner.posting-proyek'))
            ->assertOk()->assertSee('Alur verifikasi')->assertSee('Kirim untuk verifikasi');
    }

    public function test_proyek_saya_can_be_filtered_by_status(): void
    {
        $this->withSession($this->owner())->get(route('project-owner.proyek-saya', ['status' => 'pending']))
            ->assertOk()->assertSee('Sistem Informasi Perpustakaan')->assertDontSee('Poster Kampanye');
    }

    public function test_posting_proyek_validates_and_redirects(): void
    {
        $this->withSession($this->owner())->post(route('project-owner.posting-proyek.store'), ['action' => 'submit'])
            ->assertSessionHasErrors(['name', 'description', 'category', 'max_members']);

        $this->withSession($this->owner())->post(route('project-owner.posting-proyek.store'), [
            'name' => 'Aplikasi Kasir', 'description' => 'Deskripsi', 'category' => 'Aplikasi Web',
            'max_members' => 4, 'action' => 'submit',
        ])->assertRedirect(route('project-owner.proyek-saya'));
    }

    public function test_non_owner_is_redirected_to_login(): void
    {
        $this->get(route('project-owner.dashboard'))->assertRedirect('/login');

        $this->withSession(['user' => ['name' => 'Mahasiswa', 'role' => 'mahasiswa']])
            ->get(route('project-owner.posting-proyek'))->assertRedirect('/login');
    }
}
