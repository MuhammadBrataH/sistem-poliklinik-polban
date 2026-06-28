<?php

namespace Database\Seeders;

use App\Models\Poli;
use App\Models\Tindakan;
use Illuminate\Database\Seeder;

class TindakanSeeder extends Seeder
{
    public function run(): void
    {
        $poliUmum = Poli::where('nama_poli', 'Poli Umum')->first();
        $poliGigi = Poli::where('nama_poli', 'Poli Gigi')->first();

        if ($poliUmum) {
            Tindakan::insert([
                ['poli_id' => $poliUmum->id, 'nama_tindakan' => 'Konsultasi Dokter Umum', 'tarif' => 30000],
                ['poli_id' => $poliUmum->id, 'nama_tindakan' => 'Suntik Vitamin', 'tarif' => 45000],
                ['poli_id' => $poliUmum->id, 'nama_tindakan' => 'Perawatan Luka Sedang', 'tarif' => 50000],
            ]);
        }

        if ($poliGigi) {
            Tindakan::insert([
                ['poli_id' => $poliGigi->id, 'nama_tindakan' => 'Cabut Gigi Dewasa', 'tarif' => 150000],
                ['poli_id' => $poliGigi->id, 'nama_tindakan' => 'Tambal Gigi Biasa', 'tarif' => 200000],
                ['poli_id' => $poliGigi->id, 'nama_tindakan' => 'Pembersihan Karang Gigi (Scaling)', 'tarif' => 250000],
            ]);
        }
    }
}
