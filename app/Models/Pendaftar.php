<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftar extends Model
{
    protected $table = 'pendaftar';

    protected $fillable = [
        'pendaftar_id',
        'nama_anggota',
        'no_identitas',
        'jenis_identitas',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_perkawinan',
        'kewarganegaraan',
        'nama_ibu_kandung',
        'alamat_rumah',
        'kota',
        'kode_pos',
        'email',
        'no_telp',
        'status_pendaftaran',
        'alasan_tolak',
        'id_anggota',
        'tgl_daftar',
        'foto_identitas',
        'foto_npwp',
    ];
}
