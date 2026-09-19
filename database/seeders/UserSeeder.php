<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'nama'     => 'Admin Koperasi',
            'username' => 'admin',
            'email'    => 'admin@bmtalittihad.id',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // Anggota 1
        User::create([
            'nama'     => 'Putri Agustin',
            'username' => 'anggota',
            'email'    => 'putri@email.com',
            'password' => Hash::make('anggota123'),
            'role'     => 'anggota',
        ]);

        // Anggota 2
        User::create([
            'nama'     => 'Sari Wulandari',
            'username' => 'sari',
            'email'    => 'sari@email.com',
            'password' => Hash::make('sari123'),
            'role'     => 'anggota',
        ]);
    }
}
