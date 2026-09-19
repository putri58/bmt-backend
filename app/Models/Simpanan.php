<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    protected $table = 'simpanan';

    protected $fillable = [
        'anggota_id',
        'kode_simpanan',
        'jenis_simpanan',
        'jumlah',
        'tipe',
        'keterangan',
        'tanggal',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
