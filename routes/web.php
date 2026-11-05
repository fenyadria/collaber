<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProjectOwnerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/mahasiswa/dashboard', function () {
    if (!session()->has('user')) {
        return redirect('/login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
    }
    
    $user = session('user');
    return view('mahasiswa.dashboard', compact('user'));
});

Route::get('/mahasiswa/cari-proyek', function () {
    if (!session()->has('user')) {
        return redirect('/login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
    }
    
    $user = session('user');
    return view('mahasiswa.cari-proyek', compact('user'));
});

Route::prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/verifikasi-proyek', 'verifikasiProyek')->name('verifikasi-proyek');
    Route::get('/kategori-skill', 'kategoriSkill')->name('kategori-skill');
});

Route::prefix('project-owner')->name('project-owner.')->controller(ProjectOwnerController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/proyek-saya', 'proyekSaya')->name('proyek-saya');
    Route::get('/posting-proyek', 'postingProyek')->name('posting-proyek');
    Route::post('/posting-proyek', 'simpanProyek')->name('posting-proyek.store');
});
