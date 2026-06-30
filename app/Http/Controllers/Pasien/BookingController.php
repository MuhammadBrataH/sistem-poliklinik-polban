<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\JadwalPraktik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Menampilkan form pendaftaran antrean.
     */
    public function create()
    {
        $today = Carbon::today()->toDateString();
        
        // Ambil jadwal praktik yang tersedia hari ini atau ke depannya, sisa kuota > 0
        $jadwals = JadwalPraktik::with(['dokter.user', 'dokter.poli'])
            ->where('tanggal', '>=', $today)
            ->where('sisa_kuota', '>', 0)
            ->get();

        return Inertia::render('Pasien/Antrean/Create', [
            'jadwals' => $jadwals
        ]);
    }

    /**
     * Logika Booking Antrean (FCFS) - INI KRUSIAL
     *
     * MENGAPA KITA MENGGUNAKAN DB TRANSACTION & LOCK FOR UPDATE:
     * Pada sistem antrean *First Come First Serve* yang sibuk, ratusan pasien bisa saja
     * mengklik tombol "Daftar" pada detik yang sama untuk satu Jadwal Praktik yang sama.
     * Jika sisa kuota tinggal 1, dan dua orang mengklik bersamaan, tanpa penguncian (lock),
     * keduanya akan lolos pengecekan (sisa_kuota > 0) dan kuota akan tembus (overbook) menjadi -1.
     * 
     * `DB::transaction()` membungkus seluruh operasi menjadi satu kesatuan (atomik).
     * `lockForUpdate()` (Pessimistic Locking) akan mengunci baris jadwal_praktiks tersebut di database.
     * Siapa pun yang mengklik 1 milidetik lebih lambat akan dipaksa menunggu (oleh MySQL) 
     * sampai transaksi pertama selesai, baru ia bisa membaca baris tersebut.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_praktik_id' => 'required|exists:jadwal_praktiks,id',
        ]);

        $user = Auth::user();
        $pasien = $user->pasien;

        if (!$pasien) {
            return redirect()->route('pasien.profile.setup')->with('error', 'Silakan lengkapi profil Anda terlebih dahulu.');
        }

        $jadwalId = $request->jadwal_praktik_id;
        $today = Carbon::today()->toDateString();

        // 1. Validasi: Pastikan pasien belum memiliki antrean aktif di hari yang sama agar tidak spamming.
        $punyaAntreanAktif = Antrean::where('pasien_id', $pasien->id)
            ->whereHas('jadwalPraktik', function ($q) use ($today) {
                $q->where('tanggal', $today);
            })
            ->whereNotIn('status', ['selesai', 'batal'])
            ->exists();

        if ($punyaAntreanAktif) {
            return back()->with('error', 'Anda sudah memiliki antrean aktif hari ini. Silakan selesaikan atau batalkan antrean sebelumnya.');
        }

        try {
            // Memulai Database Transaction
            DB::beginTransaction();

            // a. Lock baris jadwal_praktiks yang dipilih (gunakan lockForUpdate)
            // Ini akan memastikan query lain yang mencoba melock baris ini (untuk update) akan tertahan.
            $jadwal = JadwalPraktik::with('dokter.poli')->where('id', $jadwalId)->lockForUpdate()->first();

            // Validasi tambahan: Pastikan jadwalnya adalah jadwal hari ini atau ke depan
            if ($jadwal->tanggal < $today) {
                DB::rollBack();
                return back()->with('error', 'Jadwal praktik ini sudah kedaluwarsa.');
            }

            // b. Cek apakah sisa_kuota > 0. Jika habis, kembalikan respon error.
            if ($jadwal->sisa_kuota <= 0) {
                DB::rollBack(); // Batalkan seluruh query di blok ini
                return back()->with('error', 'Maaf, kuota antrean untuk jadwal ini sudah penuh.');
            }

            // c. Generate nomor_antrean secara otomatis berurutan (Misal: format "U-001" untuk Umum, "G-001" untuk Gigi).
            // Ambil awalan dari nama Poli (misal "Poli Gigi" -> Ambil huruf "G")
            $namaPoli = $jadwal->dokter->poli->nama_poli;
            // Menghapus kata "Poli " jika ada, ambil huruf pertama, jadikan Uppercase
            $prefixPoli = strtoupper(substr(str_replace('Poli ', '', $namaPoli), 0, 1));
            
            // Hitung sudah ada berapa antrean pada jadwal ini untuk mendapatkan nomor urut.
            // Gunakan count() langsung pada database daripada memuat collection.
            $urutan = Antrean::where('jadwal_praktik_id', $jadwal->id)->count() + 1;
            
            // Format nomor: Prefix - 3 digit urutan (misal: G-001)
            $nomorAntrean = sprintf("%s-%03d", $prefixPoli, $urutan);

            // d. Insert ke tabel antreans dengan status default menunggu_konfirmasi.
            Antrean::create([
                'pasien_id' => $pasien->id,
                'jadwal_praktik_id' => $jadwal->id,
                'nomor_antrean' => $nomorAntrean,
                'status' => 'menunggu_konfirmasi',
            ]);

            // e. Kurangi (decrement) nilai sisa_kuota di tabel jadwal_praktiks.
            $jadwal->sisa_kuota -= 1;
            $jadwal->save(); // Simpan perubahan kuota

            // Commit transaksi, kunci dilepaskan.
            DB::commit();

            return redirect()->route('pasien.dashboard')->with('success', 'Berhasil mendaftar antrean! Nomor antrean Anda adalah ' . $nomorAntrean);

        } catch (\Exception $e) {
            // Jika ada error sistem (database down, syntax error), batalkan semua operasi
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat mendaftar. Silakan coba lagi. ' . $e->getMessage());
        }
    }
}
