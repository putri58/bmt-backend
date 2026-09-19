<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'anggota';

    protected $fillable = [
        'id_anggota',
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
        'simpanan',
        'status_anggota',
        'tgl_daftar',
        'tgl_disetujui',
    ];

    public function simpananList()
    {
        return $this->hasMany(Simpanan::class);
    }

    public function pinjamanList()
    {
        return $this->hasMany(Pinjaman::class);
    }

    public function transaksiList()
    {
        return $this->hasMany(Transaksi::class);
    }
}
