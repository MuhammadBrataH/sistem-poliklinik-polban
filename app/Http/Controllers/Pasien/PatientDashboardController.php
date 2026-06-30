<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Antrean;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PatientDashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Pasien (Live Queue Tracker & Kartu Antrean).
     *
     * MENGAPA KITA MELAKUKAN INI:
     * 1. Dashboard pasien bertindak sebagai 'Pusat Informasi' pribadi.
     * 2. Pasien butuh tahu: "Apakah saya punya antrean hari ini?" (Query Kartu Pasien).
     * 3. Pasien butuh tahu: "Nomor berapa yang sekarang sedang dilayani di poli saya?" (Query Live Tracker).
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $user = Auth::user();
        $pasien = $user->pasien;

        // Jika pasien belum melengkapi profil, bisa diarahkan ke halaman setup profil.
        // (Biasanya kita tangani via Middleware, tapi untuk safety net kita cek juga di sini)
        if (!$pasien) {
            return redirect()->route('pasien.profile.setup'); // Asumsi rute ini akan dibuat
        }

        // QUERY 2 (Kartu Pasien): Cek antrean aktif milik user HARI INI
        // Kita abaikan status 'selesai', 'batal', karena itu masuk histori.
        $antreanAktifSaya = Antrean::with(['jadwalPraktik.dokter.poli', 'jadwalPraktik.dokter.user'])
            ->where('pasien_id', $pasien->id)
            ->whereHas('jadwalPraktik', function($q) use ($today) {
                $q->where('tanggal', $today);
            })
            ->whereNotIn('status', ['selesai', 'batal'])
            ->first();

        $antreanSedangBerjalan = null;

        // QUERY 1 (Live Tracker): Jika pasien punya antrean aktif, cari nomor berapa yang SEDANG DIPERIKSA di Poli/Jadwal yang sama
        if ($antreanAktifSaya) {
            $jadwalId = $antreanAktifSaya->jadwal_praktik_id;

            // Cari antrean pada jadwal yang sama, yang statusnya sedang 'diperiksa'
            $antreanSedangBerjalan = Antrean::where('jadwal_praktik_id', $jadwalId)
                ->where('status', 'diperiksa')
                ->orderBy('updated_at', 'desc')
                ->first();
        }

        return Inertia::render('Pasien/Dashboard', [
            'antreanAktifSaya' => $antreanAktifSaya,
            'antreanSedangBerjalan' => $antreanSedangBerjalan,
        ]);
    }

    /**
     * Menu Riwayat Kunjungan (Tugas No. 4)
     * Mengambil data histori antrean milik pasien.
     */
    public function history()
    {
        $pasien = Auth::user()->pasien;

        if (!$pasien) {
            return redirect()->route('pasien.profile.setup');
        }

        // Ambil histori antrean (selesai, batal, menunggu_obat)
        // Diurutkan dari yang terbaru (created_at DESC)
        $riwayat = Antrean::with(['jadwalPraktik.dokter.poli', 'jadwalPraktik.dokter.user'])
            ->where('pasien_id', $pasien->id)
            ->whereIn('status', ['selesai', 'batal', 'menunggu_obat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Pasien/History', [
            'riwayat' => $riwayat,
        ]);
    }
}
