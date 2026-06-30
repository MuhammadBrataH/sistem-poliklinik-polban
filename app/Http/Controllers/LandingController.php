<?php

namespace App\Http\Controllers;

use App\Models\JadwalPraktik;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class LandingController extends Controller
{
    /**
     * Menampilkan Landing Page publik.
     * 
     * MENGAPA KITA MELAKUKAN INI:
     * Halaman landing adalah titik masuk utama (beranda). Pasien atau pengunjung
     * ingin melihat jadwal dokter yang aktif "hari ini". Oleh karena itu, kita 
     * mengambil data JadwalPraktik spesifik untuk hari ini, dilengkapi dengan 
     * relasi (Eager Loading) ke Dokter dan Poli untuk menghindari N+1 query problem.
     */
    public function index()
    {
        // 1. Dapatkan tanggal hari ini menggunakan library Carbon bawaan Laravel.
        $today = Carbon::today()->toDateString(); // Format: YYYY-MM-DD

        // 2. Query ke database:
        // - where('tanggal', $today) -> filter hanya jadwal hari ini
        // - with(['dokter.user', 'dokter.poli']) -> Eager loading relasi bertingkat.
        //   Ini sangat penting agar kita tidak melakukan query database berulang-ulang 
        //   di dalam loop Vue/Blade nanti (menjaga performa tetap secepat kilat).
        $jadwalHariIni = JadwalPraktik::with(['dokter.user', 'dokter.poli'])
            ->where('tanggal', $today)
            ->get();

        // 3. Render komponen Vue 'Welcome' (atau Landing) via Inertia.
        // Kita juga bisa mengirimkan flag canLogin/canRegister jika diperlukan.
        return Inertia::render('Welcome', [
            'jadwalHariIni' => $jadwalHariIni,
        ]);
    }
}
