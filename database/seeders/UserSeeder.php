<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin — bisa lihat semua cabang
        User::create([
            'nama'     => 'Super Admin',
            'username' => 'superadmin',
            'email'    => 'superadmin@bmtalittihad.id',
            'password' => Hash::make('super123'),
            'role'     => 'superadmin',
            'kantor'   => null,
            'status'   => 'aktif',
        ]);

        // Admin Pusat
        User::create([
            'nama'     => 'Admin Kantor Pusat',
            'username' => 'admin.pusat',
            'email'    => 'pusat@bmtalittihad.id',
            'password' => Hash::make('pusat123'),
            'role'     => 'admin',
            'kantor'   => 'pusat',
            'status'   => 'aktif',
        ]);

        // Admin Rumbai
        User::create([
            'nama'     => 'Admin Cabang Rumbai',
            'username' => 'admin.rumbai',
            'email'    => 'rumbai@bmtalittihad.id',
            'password' => Hash::make('rumbai123'),
            'role'     => 'admin',
            'kantor'   => 'rumbai',
            'status'   => 'aktif',
        ]);

        // Admin Panam
        User::create([
            'nama'     => 'Admin Cabang Panam',
            'username' => 'admin.panam',
            'email'    => 'panam@bmtalittihad.id',
            'password' => Hash::make('panam123'),
            'role'     => 'admin',
            'kantor'   => 'panam',
            'status'   => 'aktif',
        ]);

        // Admin Duri
        User::create([
            'nama'     => 'Admin Cabang Duri',
            'username' => 'admin.duri',
            'email'    => 'duri@bmtalittihad.id',
            'password' => Hash::make('duri123'),
            'role'     => 'admin',
            'kantor'   => 'duri',
            'status'   => 'aktif',
        ]);

        // Admin Cibubur
        User::create([
            'nama'     => 'Admin Cabang Cibubur',
            'username' => 'admin.cibubur',
            'email'    => 'cibubur@bmtalittihad.id',
            'password' => Hash::make('cibubur123'),
            'role'     => 'admin',
            'kantor'   => 'cibubur',
            'status'   => 'aktif',
        ]);
    }
}
