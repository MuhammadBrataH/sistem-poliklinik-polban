<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

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
    Route::get('/pasien/home', function () {
        return Inertia::render('Dashboard'); // Sementara render dashboard
    })->name('pasien.home');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
