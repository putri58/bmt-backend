<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // Ambil data profil anggota yang sedang login
    public function profil(Request $request)
    {
        $anggotaId = $request->user()->anggota_id;

        if (!$anggotaId) {
            return response()->json(['message' => 'Data anggota tidak ditemukan'], 404);
        }

        $anggota = Anggota::where('id_anggota', $anggotaId)->first();

        if (!$anggota) {
            return response()->json(['message' => 'Data anggota tidak ditemukan'], 404);
        }

        return response()->json($anggota);
    }

    // Ambil riwayat simpanan anggota yang sedang login
    public function simpanan(Request $request)
    {
        $anggotaId = $request->user()->anggota_id;
        $anggota   = Anggota::where('id_anggota', $anggotaId)->first();

        if (!$anggota) {
            return response()->json(['message' => 'Data anggota tidak ditemukan'], 404);
        }

        $simpanan = Simpanan::where('anggota_id', $anggota->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'total_simpanan' => $anggota->simpanan,
            'riwayat'        => $simpanan,
        ]);
    }

    // Ambil riwayat pinjaman anggota yang sedang login
    public function pinjaman(Request $request)
    {
        $anggotaId = $request->user()->anggota_id;
        $anggota   = Anggota::where('id_anggota', $anggotaId)->first();

        if (!$anggota) {
            return response()->json(['message' => 'Data anggota tidak ditemukan'], 404);
        }

        $pinjaman = Pinjaman::where('anggota_id', $anggota->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pinjaman);
    }

    // Ambil riwayat angsuran anggota yang sedang login
    public function angsuran(Request $request)
    {
        $anggotaId = $request->user()->anggota_id;
        $anggota   = Anggota::where('id_anggota', $anggotaId)->first();

        if (!$anggota) {
            return response()->json(['message' => 'Data anggota tidak ditemukan'], 404);
        }

        // Ambil semua angsuran dari semua pinjaman milik anggota ini
        $pinjamanIds = Pinjaman::where('anggota_id', $anggota->id)->pluck('id');

        $angsuran = Angsuran::whereIn('pinjaman_id', $pinjamanIds)
            ->with('pinjaman')
            ->orderBy('jatuh_tempo', 'asc')
            ->get();

        return response()->json($angsuran);
    }

    // Ambil riwayat transaksi anggota yang sedang login
    public function transaksi(Request $request)
    {
        $anggotaId = $request->user()->anggota_id;
        $anggota   = Anggota::where('id_anggota', $anggotaId)->first();

        if (!$anggota) {
            return response()->json(['message' => 'Data anggota tidak ditemukan'], 404);
        }

        $transaksi = Transaksi::where('anggota_id', $anggota->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json($transaksi);
    }

    // Dashboard — ringkasan semua data anggota
    public function dashboard(Request $request)
    {
        $anggotaId = $request->user()->anggota_id;
        $anggota   = Anggota::where('id_anggota', $anggotaId)->first();

        if (!$anggota) {
            return response()->json(['message' => 'Data anggota tidak ditemukan'], 404);
        }

        // Pinjaman aktif
        $pinjamanAktif = Pinjaman::where('anggota_id', $anggota->id)
            ->where('status', 'Disetujui')
            ->count();

        // Angsuran jatuh tempo terdekat
        $pinjamanIds = Pinjaman::where('anggota_id', $anggota->id)->pluck('id');
        $angsuranTerdekat = Angsuran::whereIn('pinjaman_id', $pinjamanIds)
            ->where('status', 'Belum Bayar')
            ->orderBy('jatuh_tempo', 'asc')
            ->first();

        // Transaksi terbaru (5)
        $transaksiTerbaru = Transaksi::where('anggota_id', $anggota->id)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'anggota'           => $anggota,
            'pinjaman_aktif'    => $pinjamanAktif,
            'angsuran_terdekat' => $angsuranTerdekat,
            'transaksi_terbaru' => $transaksiTerbaru,
        ]);
    }
}
