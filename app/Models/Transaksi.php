<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'anggota_id',
        'kode_transaksi',
        'jenis',
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
