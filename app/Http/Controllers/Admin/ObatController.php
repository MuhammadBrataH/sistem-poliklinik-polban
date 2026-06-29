<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::query();
        
        if ($request->search) {
            $query->where('nama_obat', 'like', '%' . $request->search . '%');
        }
        
        $obats = $query->paginate(10)->withQueryString();
        
        return Inertia::render('Admin/Obat/Index', [
            'obats' => $obats,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
        ]);

        Obat::create([
            'nama_obat' => $request->nama_obat,
            'satuan' => $request->satuan,
            'stok' => $request->stok,
        ]);

        return back()->with('success', 'Obat berhasil ditambahkan.');
    }

    public function update(Request $request, Obat $obat)
    {
        $request->validate([
            'nama_obat' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
        ]);

        $obat->update([
            'nama_obat' => $request->nama_obat,
            'satuan' => $request->satuan,
            'stok' => $request->stok,
        ]);

        return back()->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Obat $obat)
    {
        $obat->delete();
        return back()->with('success', 'Obat berhasil dihapus.');
    }
}
