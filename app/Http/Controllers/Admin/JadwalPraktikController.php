<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPraktik;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JadwalPraktikController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalPraktik::with(['dokter.user', 'dokter.poli']);
        
        if ($request->search) {
            $query->whereHas('dokter.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhere('tanggal', 'like', '%' . $request->search . '%');
        }
        
        $jadwals = $query->paginate(10)->withQueryString();
        $dokters = Dokter::with(['user', 'poli'])->get();
        
        return Inertia::render('Admin/JadwalPraktik/Index', [
            'jadwals' => $jadwals,
            'dokters' => $dokters,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dokter_id' => 'required|exists:dokters,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kuota' => 'required|integer|min:1',
        ]);

        // Cek apakah ada jadwal yang sama untuk dokter di tanggal yang sama yang tumpang tindih
        $konflik = JadwalPraktik::where('dokter_id', $request->dokter_id)
            ->where('tanggal', $request->tanggal)
            ->where(function($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('jam_mulai', '<=', $request->jam_mulai)
                         ->where('jam_selesai', '>=', $request->jam_selesai);
                  });
            })->exists();

        if ($konflik) {
            return back()->withErrors(['jam_mulai' => 'Dokter sudah memiliki jadwal yang bentrok di tanggal ini.']);
        }

        JadwalPraktik::create([
            'dokter_id' => $request->dokter_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kuota' => $request->kuota,
            'sisa_kuota' => $request->kuota,
        ]);

        return back()->with('success', 'Jadwal Praktik berhasil ditambahkan.');
    }

    public function update(Request $request, JadwalPraktik $jadwal_praktik)
    {
        $request->validate([
            'dokter_id' => 'required|exists:dokters,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kuota' => 'required|integer|min:1',
        ]);

        $konflik = JadwalPraktik::where('dokter_id', $request->dokter_id)
            ->where('tanggal', $request->tanggal)
            ->where('id', '!=', $jadwal_praktik->id)
            ->where(function($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('jam_mulai', '<=', $request->jam_mulai)
                         ->where('jam_selesai', '>=', $request->jam_selesai);
                  });
            })->exists();

        if ($konflik) {
            return back()->withErrors(['jam_mulai' => 'Dokter sudah memiliki jadwal yang bentrok di tanggal ini.']);
        }

        // Sesuaikan sisa kuota dengan selisih kuota baru
        $selisih = $request->kuota - $jadwal_praktik->kuota;
        $sisa_baru = $jadwal_praktik->sisa_kuota + $selisih;
        
        // Pastikan sisa kuota tidak negatif jika kuota diturunkan drastis
        if ($sisa_baru < 0) {
            return back()->withErrors(['kuota' => 'Kuota baru terlalu kecil, pasien sudah terdaftar melebihi kuota ini.']);
        }

        $jadwal_praktik->update([
            'dokter_id' => $request->dokter_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kuota' => $request->kuota,
            'sisa_kuota' => $sisa_baru,
        ]);

        return back()->with('success', 'Jadwal Praktik berhasil diperbarui.');
    }

    public function destroy(JadwalPraktik $jadwal_praktik)
    {
        $jadwal_praktik->delete();
        return back()->with('success', 'Jadwal Praktik berhasil dihapus.');
    }
}
