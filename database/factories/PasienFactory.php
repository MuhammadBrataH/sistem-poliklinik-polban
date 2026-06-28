<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasienFactory extends Factory
{
    public function definition(): array
    {
        // 70% pasien punya akun (online), 30% pasien go-show (offline/tanpa akun)
        $hasAccount = fake()->boolean(70);

        return [
            'user_id' => $hasAccount ? User::factory()->state(['role' => 'pasien']) : null,
            'no_rm' => 'RM-2026-' . fake()->unique()->numerify('####'),
            'nik' => fake()->unique()->numerify('327#############'),
            'nama_lengkap' => fake()->name(),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->date('Y-m-d', '2005-01-01'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'kategori' => fake()->randomElement(['mahasiswa', 'pegawai', 'keluarga_pegawai', 'umum']),
            'nim_nip' => fake()->optional()->numerify('2026####'),
            'prodi_unit' => fake()->optional()->jobTitle(),
            'no_telp' => fake()->phoneNumber(),
            'alamat' => fake()->address(),
            'status_bpjs' => fake()->randomElement(['Aktif', 'Tidak Aktif', null]),
            'nama_penanggung' => fake()->optional()->name(),
            'hubungan_penanggung' => fake()->optional()->randomElement(['Ayah', 'Ibu', 'Suami', 'Istri']),
        ];
    }
}
