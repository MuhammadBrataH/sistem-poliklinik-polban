<?php

namespace Database\Seeders;

use App\Models\Obat;
use Illuminate\Database\Seeder;

class ObatSeeder extends Seeder
{
    public function run(): void
    {
        // Sesuai kesepakatan, kita butuh 20 data obat.
        // Daripada hardcode, kita gunakan Factory yang sudah disiapkan.
        Obat::factory()->count(20)->create();
    }
}
