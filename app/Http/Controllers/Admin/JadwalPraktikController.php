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
            })->orWhere('hari', 'like', '%' . $request->search . '%');
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
            'hari' => 'required|string|max:20',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'status' => 'required|boolean',
        ]);

        // Cek apakah ada jadwal yang sama untuk dokter di hari yang sama yang tumpang tindih
        $konflik = JadwalPraktik::where('dokter_id', $request->dokter_id)
            ->where('hari', $request->hari)
            ->where(function($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('jam_mulai', '<=', $request->jam_mulai)
                         ->where('jam_selesai', '>=', $request->jam_selesai);
                  });
            })->exists();

        if ($konflik) {
            return back()->withErrors(['jam_mulai' => 'Dokter sudah memiliki jadwal yang bentrok di hari ini.']);
        }

        JadwalPraktik::create([
            'dokter_id' => $request->dokter_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Jadwal Praktik berhasil ditambahkan.');
    }

    public function update(Request $request, JadwalPraktik $jadwal_praktik)
    {
        $request->validate([
            'dokter_id' => 'required|exists:dokters,id',
            'hari' => 'required|string|max:20',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'status' => 'required|boolean',
        ]);

        $konflik = JadwalPraktik::where('dokter_id', $request->dokter_id)
            ->where('hari', $request->hari)
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
            return back()->withErrors(['jam_mulai' => 'Dokter sudah memiliki jadwal yang bentrok di hari ini.']);
        }

        $jadwal_praktik->update([
            'dokter_id' => $request->dokter_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Jadwal Praktik berhasil diperbarui.');
    }

    public function destroy(JadwalPraktik $jadwal_praktik)
    {
        $jadwal_praktik->delete();
        return back()->with('success', 'Jadwal Praktik berhasil dihapus.');
    }
}
