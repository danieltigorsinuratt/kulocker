<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LockerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KeluhanController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PasswordController;

// Public Landing Page
Route::get('/', [DashboardController::class, 'landing'])->name('landing');

// Authentication Routes
Route::get('/sign-in', [AuthController::class, 'showLogin'])->name('sign-in');
Route::post('/sign-in', [AuthController::class, 'login'])->name('login.post');
Route::get('/sign-up', [AuthController::class, 'showRegister'])->name('register');
Route::post('/sign-up', [AuthController::class, 'register'])->name('register.post');
Route::get('/auth/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/konfirmasi', [AuthController::class, 'konfirmasi'])->name('konfirmasi');

// Password Reset Routes
Route::get('/forgot-password', [PasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'sendResetCode'])->name('password.email');
Route::get('/verify-code', [PasswordController::class, 'showVerifyForm'])->name('password.verify');
Route::post('/verify-code', [PasswordController::class, 'verifyCode'])->name('password.verify.post');
Route::get('/reset-password', [PasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->name('password.update');

// Protected User Routes (AuthMiddleware)
Route::middleware(['auth.kulocker'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'main'])->name('dashboard');
    Route::get('/locker-selection', [LockerController::class, 'selection'])->name('locker.selection');
    Route::post('/api/proses-sewa', [LockerController::class, 'sewa'])->name('locker.sewa');
    Route::match(['get', 'post'], '/proses-sukses', [LockerController::class, 'prosesSukses'])->name('locker.sukses');
    Route::get('/tiket-saya', [LockerController::class, 'tiket'])->name('tiket');
    Route::post('/tiket/selesai', [LockerController::class, 'selesaiSewa'])->name('locker.selesai');
    Route::get('/riwayat-sewa', [LockerController::class, 'riwayat'])->name('locker.riwayat');
    
    // Profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Keluhan & Pengumuman
    Route::get('/keluhan', [KeluhanController::class, 'index'])->name('keluhan');
    Route::post('/keluhan', [KeluhanController::class, 'submit'])->name('keluhan.post');
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman');
});

// Protected Admin Routes (AdminMiddleware)
Route::middleware(['admin.kulocker'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::post('/admin', [AdminController::class, 'handlePost'])->name('admin.post');
    Route::get('/admin/tambah-locker', [AdminController::class, 'showTambahLocker'])->name('admin.tambah-locker');
    Route::post('/admin/tambah-locker', [AdminController::class, 'tambahLockerPost'])->name('admin.tambah-locker.post');
    Route::get('/admin/update-status', [AdminController::class, 'updateStatus'])->name('admin.update-status');
});

// Cron Jobs API
Route::get('/api/cron-pengingat', [LockerController::class, 'cron'])->name('api.cron-pengingat');
