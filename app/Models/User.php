<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role',
        'kantor',
        'status',
        'pendaftar_id',
        'anggota_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
