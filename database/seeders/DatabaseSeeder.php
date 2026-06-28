<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Pasien;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Data Statis
        $this->call([
            RoleSeeder::class,
            PoliSeeder::class,
            ObatSeeder::class,
            TindakanSeeder::class,
        ]);

        // 2. Data Dinamis (Sesuai kesepakatan: 3 Dokter & 10 Pasien)
        // Dokter & Pasien secara otomatis akan dibuatkan Akun (User) oleh Factory masing-masing.
        Dokter::factory()->count(3)->create();
        Pasien::factory()->count(10)->create();
    }
}
