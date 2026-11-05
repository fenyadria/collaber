<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
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