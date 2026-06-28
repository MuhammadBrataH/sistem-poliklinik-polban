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
