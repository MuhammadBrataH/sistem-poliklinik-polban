<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pasien;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input kompleks sesuai PRD & Skema DB
        $request->validate([
            'username' => 'required|string|max:255|unique:'.User::class, // NIM/NIP/NIK
            'name' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nik' => 'required|string|unique:pasiens,nik',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'kategori' => 'required|in:mahasiswa,pegawai,keluarga_pegawai,umum',
            'no_telp' => 'required|string|max:20',
            'alamat' => 'required|string',
        ]);

        try {
            // 2. Gunakan DB Transaction agar konsisten (Atomic Operation)
            // Konsep Clean Code: Jika tabel pasien gagal, tabel user tidak akan tersimpan (rollback).
            DB::beginTransaction();

            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'role' => 'pasien',
                'is_active' => true,
            ]);

            // 3. Auto-generate Nomor Rekam Medis (RM)
            // Pola: RM-YYYY-0001
            $latestPasien = Pasien::orderBy('id', 'desc')->first();
            // Ekstrak angka urut terakhir jika ada
            $nextNumber = $latestPasien ? (int) substr($latestPasien->no_rm, 8) + 1 : 1;
            $no_rm = 'RM-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            Pasien::create([
                'user_id' => $user->id,
                'no_rm' => $no_rm,
                'nik' => $request->nik,
                'nama_lengkap' => $request->name,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'kategori' => $request->kategori,
                // Jika kategori mhs/pegawai, otomatis isi nim_nip dengan username.
                'nim_nip' => in_array($request->kategori, ['mahasiswa', 'pegawai']) ? $request->username : null,
                'prodi_unit' => $request->prodi_unit,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat,
                'status_bpjs' => $request->status_bpjs,
                'nama_penanggung' => $request->nama_penanggung,
                'hubungan_penanggung' => $request->hubungan_penanggung,
            ]);

            DB::commit();

            event(new Registered($user));

            Auth::login($user);

            // Redirect ke portal pasien
            return redirect(route('pasien.home', absolute: false));
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'username' => 'Gagal mendaftarkan akun. Silakan periksa kembali data Anda.',
            ]);
        }
    }
}
