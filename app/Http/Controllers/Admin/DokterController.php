<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\User;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        $query = Dokter::with(['user', 'poli']);
        
        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            })->orWhere('spesialisasi', 'like', '%' . $request->search . '%');
        }
        
        $dokters = $query->paginate(10)->withQueryString();
        $polis = Poli::all();
        
        return Inertia::render('Admin/Dokter/Index', [
            'dokters' => $dokters,
            'polis' => $polis,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:8',
            'poli_id' => 'required|exists:polis,id',
            'spesialisasi' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'dokter',
                'is_active' => true,
            ]);

            Dokter::create([
                'user_id' => $user->id,
                'poli_id' => $request->poli_id,
                'spesialisasi' => $request->spesialisasi,
            ]);
        });

        return back()->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function update(Request $request, Dokter $dokter)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $dokter->user_id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $dokter->user_id,
            'password' => 'nullable|string|min:8',
            'poli_id' => 'required|exists:polis,id',
            'spesialisasi' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $dokter) {
            $userData = [
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $dokter->user->update($userData);

            $dokter->update([
                'poli_id' => $request->poli_id,
                'spesialisasi' => $request->spesialisasi,
            ]);
        });

        return back()->with('success', 'Data dokter berhasil diperbarui.');
    }

    public function destroy(Dokter $dokter)
    {
        DB::transaction(function () use ($dokter) {
            $userId = $dokter->user_id;
            $dokter->delete();
            User::find($userId)->delete();
        });
        
        return back()->with('success', 'Dokter berhasil dihapus.');
    }
}
