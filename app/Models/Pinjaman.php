<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjaman';

    protected $fillable = [
        'anggota_id',
        'kode_pinjaman',
        'jumlah_pinjaman',
        'margin',
        'tenor_bulan',
        'angsuran_per_bulan',
        'status',
        'tgl_pengajuan',
        'tgl_disetujui',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function angsuranList()
    {
        return $this->hasMany(Angsuran::class);
    }
}
