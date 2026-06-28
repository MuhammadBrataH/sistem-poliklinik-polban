<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Poli;
use Illuminate\Database\Eloquent\Factories\Factory;

class DokterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'dokter']),
            'poli_id' => Poli::inRandomOrder()->first()->id ?? Poli::factory(),
            'spesialisasi' => fake()->randomElement(['Spesialis Umum', 'Spesialis Gigi', 'Penyakit Dalam']),
        ];
    }
}
