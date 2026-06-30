<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tindakan;
use App\Models\Poli;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TindakanController extends Controller
{
    public function index(Request $request)
    {
        $query = Tindakan::with('poli');
        
        if ($request->search) {
            $query->where('nama_tindakan', 'like', '%' . $request->search . '%');
        }
        
        $tindakans = $query->paginate(10)->withQueryString();
        $polis = Poli::all();
        
        return Inertia::render('Admin/Tindakan/Index', [
            'tindakans' => $tindakans,
            'polis' => $polis,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tindakan' => 'required|string|max:255',
            'poli_id' => 'required|exists:polis,id',
            'tarif' => 'required|numeric|min:0',
        ]);

        Tindakan::create([
            'nama_tindakan' => $request->nama_tindakan,
            'poli_id' => $request->poli_id,
            'tarif' => $request->tarif,
        ]);

        return back()->with('success', 'Tindakan berhasil ditambahkan.');
    }

    public function update(Request $request, Tindakan $tindakan)
    {
        $request->validate([
            'nama_tindakan' => 'required|string|max:255',
            'poli_id' => 'required|exists:polis,id',
            'tarif' => 'required|numeric|min:0',
        ]);

        $tindakan->update([
            'nama_tindakan' => $request->nama_tindakan,
            'poli_id' => $request->poli_id,
            'tarif' => $request->tarif,
        ]);

        return back()->with('success', 'Data tindakan berhasil diperbarui.');
    }

    public function destroy(Tindakan $tindakan)
    {
        $tindakan->delete();
        return back()->with('success', 'Tindakan berhasil dihapus.');
    }
}
