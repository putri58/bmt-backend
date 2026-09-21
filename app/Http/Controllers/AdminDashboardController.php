<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pendaftar;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Total statistik
        $totalAnggota  = Anggota::count();
        $totalSimpanan = Anggota::sum('simpanan');
        $totalPinjaman = Pinjaman::where('status', 'Disetujui')->sum('jumlah_pinjaman');
        $totalTransaksi = Transaksi::count();
        $pengajuanMenunggu = Pendaftar::where('status_pendaftaran', 'Menunggu')->count();

        // Pendaftar terbaru (5)
        $pendaftarTerbaru = Pendaftar::orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'pendaftar_id', 'nama_anggota', 'status_pendaftaran', 'tgl_daftar']);

        // Transaksi terbaru (5)
        $transaksiTerbaru = Transaksi::with('anggota')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Anggota terbaru (5)
        $anggotaTerbaru = Anggota::orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'id_anggota', 'nama_anggota', 'tgl_disetujui', 'status_anggota', 'simpanan']);

        return response()->json([
            'statistik' => [
                'total_anggota'       => $totalAnggota,
                'total_simpanan'      => $totalSimpanan,
                'total_pinjaman'      => $totalPinjaman,
                'total_transaksi'     => $totalTransaksi,
                'pengajuan_menunggu'  => $pengajuanMenunggu,
            ],
            'pendaftar_terbaru' => $pendaftarTerbaru,
            'transaksi_terbaru' => $transaksiTerbaru,
            'anggota_terbaru'   => $anggotaTerbaru,
        ]);
    }
}
