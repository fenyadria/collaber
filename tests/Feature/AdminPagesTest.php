<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    public function test_admin_can_open_all_three_admin_pages(): void
    {
        $session = ['user' => ['name' => 'Admin Collaber', 'role' => 'admin']];

        $this->withSession($session)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Total pengguna')
            ->assertSee('Laporan terbaru');

        $this->withSession($session)
            ->get(route('admin.verifikasi-proyek'))
            ->assertOk()
            ->assertSee('Nama Proyek')
            ->assertSee('Approve')
            ->assertSee('Reject');

        $this->withSession($session)
            ->get(route('admin.kategori-skill'))
            ->assertOk()
            ->assertSee('Aplikasi Web')
            ->assertSee('Laravel')
            ->assertSee('Tambah');
    }

    public function test_non_admin_cannot_open_admin_pages(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect('/login');

        $this->withSession(['user' => ['name' => 'Mahasiswa', 'role' => 'mahasiswa']])
            ->get(route('admin.verifikasi-proyek'))
            ->assertRedirect('/login');
    }
}
