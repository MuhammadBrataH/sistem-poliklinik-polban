<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PoliController extends Controller
{
    public function index(Request $request)
    {
        $query = Poli::query();
        
        if ($request->search) {
            $query->where('nama_poli', 'like', '%' . $request->search . '%');
        }
        
        $polis = $query->paginate(10)->withQueryString();
        
        return Inertia::render('Admin/Poli/Index', [
            'polis' => $polis,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_poli' => 'required|string|max:255|unique:polis',
        ]);

        Poli::create([
            'nama_poli' => $request->nama_poli,
        ]);

        return back()->with('success', 'Poli berhasil ditambahkan.');
    }

    public function update(Request $request, Poli $poli)
    {
        $request->validate([
            'nama_poli' => 'required|string|max:255|unique:polis,nama_poli,' . $poli->id,
        ]);

        $poli->update([
            'nama_poli' => $request->nama_poli,
        ]);

        return back()->with('success', 'Poli berhasil diperbarui.');
    }

    public function destroy(Poli $poli)
    {
        $poli->delete();
        return back()->with('success', 'Poli berhasil dihapus.');
    }
}
