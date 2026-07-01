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
    public function index(Request $request)
    {
        // Ambil parameter tanggal dari URL, default ke hari ini
        $selectedDate = $request->query('date', Carbon::today()->toDateString());

        // Ambil jadwal praktik berdasarkan tanggal yang dipilih
        $jadwal = JadwalPraktik::with(['dokter.user', 'dokter.poli'])
            ->where('tanggal', $selectedDate)
            ->get();

        return Inertia::render('Welcome', [
            'jadwalHariIni' => $jadwal,
            'selectedDate' => $selectedDate,
        ]);
    }
}
