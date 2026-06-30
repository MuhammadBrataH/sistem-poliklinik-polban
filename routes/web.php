<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');

// Route untuk Admin / Dokter / Super Admin
Route::middleware(['auth', 'verified', 'role:super_admin,admin,dokter'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('admin.dashboard');
});

// Route Khusus Super Admin & Admin (Master Data)
Route::middleware(['auth', 'verified', 'role:super_admin,admin'])->group(function () {
    Route::resource('/admin/poli', \App\Http\Controllers\Admin\PoliController::class)->except(['create', 'show', 'edit'])->names('admin.poli');
    Route::resource('/admin/dokter', \App\Http\Controllers\Admin\DokterController::class)->except(['create', 'show', 'edit'])->names('admin.dokter');
    Route::resource('/admin/tindakan', \App\Http\Controllers\Admin\TindakanController::class)->except(['create', 'show', 'edit'])->names('admin.tindakan');
    Route::resource('/admin/obat', \App\Http\Controllers\Admin\ObatController::class)->except(['create', 'show', 'edit'])->names('admin.obat');
    Route::resource('/admin/template-rujukan', \App\Http\Controllers\Admin\TemplateRujukanController::class)->except(['create', 'show', 'edit'])->names('admin.template-rujukan');
    Route::resource('/admin/jadwal-praktik', \App\Http\Controllers\Admin\JadwalPraktikController::class)->except(['create', 'show', 'edit'])->names('admin.jadwal-praktik');
});

// Route untuk Pasien
Route::middleware(['auth', 'verified', 'role:pasien'])->group(function () {
    // 1. Dashboard & History
    Route::get('/pasien/home', [\App\Http\Controllers\Pasien\PatientDashboardController::class, 'index'])->name('pasien.dashboard');
    Route::get('/pasien/history', [\App\Http\Controllers\Pasien\PatientDashboardController::class, 'history'])->name('pasien.history');
    
    // 2. Booking Antrean
    Route::get('/pasien/antrean/create', [\App\Http\Controllers\Pasien\BookingController::class, 'create'])->name('pasien.antrean.create');
    Route::post('/pasien/antrean', [\App\Http\Controllers\Pasien\BookingController::class, 'store'])->name('pasien.antrean.store');
    
    // 3. Setup Profile Pasien (sebelum bisa daftar antrean)
    Route::get('/pasien/profile/setup', function() {
        return Inertia\Inertia::render('Pasien/Profile/Setup');
    })->name('pasien.profile.setup');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
