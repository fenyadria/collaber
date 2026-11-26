<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProjectOwnerController;
use App\Http\Controllers\TeamFeedbackController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->controller(MahasiswaController::class)->group(function(){
    Route::get('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/cari-proyek', 'cariProyek')->name('cari-proyek');
    Route::post('/cari-proyek/{project}/apply', 'applyProyek')->name('cari-proyek.apply');

    Route::get('/lamaran-saya', 'lamaranSaya')->name('lamaran-saya');

    Route::get('/tim-saya', [MahasiswaController::class, 'timSaya'])->name('tim-saya');
    Route::patch('/tim-saya/tasks/{task}/status', [MahasiswaController::class, 'updateTaskStatus'])->name('tim-saya.tasks.status');
    Route::post('/tim-saya/tasks/{task}/submission', [MahasiswaController::class, 'uploadTaskSubmission'])->name('tim-saya.tasks.submission');

    Route::get('/profil-skill', 'profilSkill')->name('profil-skill');
    Route::put('/profil-skill', 'updateProfil')->name('profil-skill.update');

    Route::post('/profil-skill/skills', 'storeSkill')->name('profil-skill.skills.store');
    Route::patch('/profil-skill/skills/{skill}', 'updateSkill')->name('profil-skill.skills.update');
    Route::delete('/profil-skill/skills/{skill}', 'detachSkill')->name('profil-skill.skills.destroy');

    Route::get('/profil-skill/cv', [MahasiswaController::class, 'lihatCv'])->name('profil-skill.cv');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/verifikasi-proyek', 'verifikasiProyek')->name('verifikasi-proyek');
    Route::post('/verifikasi-proyek/{project}/status', 'updateProyekStatus')->name('verifikasi-proyek.status');
    Route::get('/kategori-skill', 'kategoriSkill')->name('kategori-skill');
    Route::post('/kategori-skill/{type}', 'storeCatalog')->name('catalog.store');
    Route::put('/kategori-skill/{type}/{item}', 'updateCatalog')->name('catalog.update');
    Route::delete('/kategori-skill/{type}/{item}', 'deleteCatalog')->name('catalog.delete');
    Route::get('/pengguna', 'pengguna')->name('pengguna');
    Route::get('/pengguna/{user}', 'detailPengguna')->name('pengguna.show');
    Route::post('/pengguna/{user}/status', 'updatePenggunaStatus')->name('pengguna.status');
    Route::get('/laporan', 'laporan')->name('laporan');
    Route::post('/laporan/{report}/status', 'updateLaporanStatus')->name('laporan.status');
});

Route::middleware(['auth', 'role:project_owner'])->prefix('project-owner')->name('project-owner.')->controller(ProjectOwnerController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/proyek-saya', 'proyekSaya')->name('proyek-saya');
    Route::get('/posting-proyek', 'postingProyek')->name('posting-proyek');
    Route::post('/posting-proyek', 'simpanProyek')->name('posting-proyek.store');
    Route::get('/proyek/{project}/edit', 'editProyek')->name('proyek.edit');
    Route::put('/proyek/{project}', 'updateProyek')->name('proyek.update');
    Route::delete('/proyek/{project}', 'hapusProyek')->name('proyek.destroy');

    Route::get('/pelamar', 'pelamar')->name('pelamar');
    Route::post('/pelamar/{application}/status', 'updatePelamarStatus')->name('pelamar.status');

    Route::get('/workspace/{project?}', 'workspace')->name('workspace');
    Route::post('/workspace/{project}/tasks', 'simpanTask')->name('workspace.tasks.store');
    Route::post('/workspace/{project}/selesai', 'selesaiProyek')->name('workspace.complete');
    Route::delete('/workspace/tasks/{task}', 'hapusTask')->name('workspace.tasks.destroy');
    Route::put('/workspace/tasks/{task}', 'updateTask')->name('workspace.tasks.update');
    Route::patch('/workspace/tasks/{task}/status', 'updateTaskStatus')->name('workspace.tasks.status');
    Route::post('/workspace/tasks/{task}/submission', 'uploadTaskSubmission')->name('workspace.tasks.submission');
});

// Laporan dan ulasan: dipakai Mahasiswa dan Project Owner, dicek lewat keanggotaan proyek.
Route::middleware('auth')->controller(TeamFeedbackController::class)->group(function () {
    Route::post('/proyek/{project}/laporan', 'storeLaporan')->name('laporan.store');
    Route::post('/proyek/{project}/ulasan', 'storeUlasan')->name('ulasan.store');
});
