<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['nama' => 'Super Admin',          'username' => 'superadmin',    'email' => 'superadmin@bmtalittihad.id', 'password' => 'super123',   'role' => 'superadmin', 'kantor' => null,      'status' => 'aktif'],
            ['nama' => 'Admin Kantor Pusat',   'username' => 'admin.pusat',   'email' => 'pusat@bmtalittihad.id',     'password' => 'pusat123',   'role' => 'admin',       'kantor' => 'pusat',   'status' => 'aktif'],
            ['nama' => 'Admin Cabang Rumbai',  'username' => 'admin.rumbai',  'email' => 'rumbai@bmtalittihad.id',    'password' => 'rumbai123',  'role' => 'admin',       'kantor' => 'rumbai',  'status' => 'aktif'],
            ['nama' => 'Admin Cabang Panam',   'username' => 'admin.panam',   'email' => 'panam@bmtalittihad.id',     'password' => 'panam123',   'role' => 'admin',       'kantor' => 'panam',   'status' => 'aktif'],
            ['nama' => 'Admin Cabang Duri',    'username' => 'admin.duri',    'email' => 'duri@bmtalittihad.id',      'password' => 'duri123',    'role' => 'admin',       'kantor' => 'duri',    'status' => 'aktif'],
            ['nama' => 'Admin Cabang Cibubur', 'username' => 'admin.cibubur', 'email' => 'cibubur@bmtalittihad.id',   'password' => 'cibubur123', 'role' => 'admin',       'kantor' => 'cibubur', 'status' => 'aktif'],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['username' => $userData['username']],
                [
                    'nama'     => $userData['nama'],
                    'email'    => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'role'     => $userData['role'],
                    'kantor'   => $userData['kantor'],
                    'status'   => $userData['status'],
                ]
            );
        }
    }
}
