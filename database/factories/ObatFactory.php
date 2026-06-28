<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ObatFactory extends Factory
{
    public function definition(): array
    {
        $obats = ['Paracetamol', 'Amoxicillin', 'Ibuprofen', 'Vitamin C', 'Antasida', 'Cetirizine', 'Omeprazole', 'Salbutamol', 'Dexamethasone', 'Cefadroxil'];
        
        return [
            'nama_obat' => fake()->randomElement($obats) . ' ' . fake()->numerify('##mg'),
            'satuan' => fake()->randomElement(['Tablet', 'Botol', 'Strip', 'Kapsul']),
            'stok' => fake()->numberBetween(10, 200),
            'gambar' => null,
        ];
    }
}
