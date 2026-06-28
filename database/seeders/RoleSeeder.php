<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123'); // Password seragam sesuai kesepakatan

        User::create([
            'username' => 'superadmin',
            'name' => 'Super Administrator',
            'password' => $password,
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'admin',
            'name' => 'Administrator Klinik',
            'password' => $password,
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
