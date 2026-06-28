<?php

namespace Database\Seeders;

use App\Models\Poli;
use Illuminate\Database\Seeder;

class PoliSeeder extends Seeder
{
    public function run(): void
    {
        $polis = [
            ['nama_poli' => 'Poli Umum'],
            ['nama_poli' => 'Poli Gigi'],
        ];

        foreach ($polis as $poli) {
            Poli::create($poli);
        }
    }
}
