<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PinjamanController extends Controller
{
    public function index()
    {
        return response()->json(
            Pinjaman::with('anggota')->orderBy('created_at', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggota_id'      => 'required|exists:anggota,id',
            'jumlah_pinjaman' => 'required|numeric|min:1',
            'margin'          => 'required|numeric|min:0',
            'tenor_bulan'     => 'required|integer|min:1',
            'tgl_pengajuan'   => 'required|date',
        ]);

        $lastPin      = Pinjaman::orderByRaw('CAST(SUBSTRING(kode_pinjaman, 5) AS UNSIGNED) DESC')->first();
        $lastPinNum   = $lastPin ? (int) substr($lastPin->kode_pinjaman, 4) : 0;
        $kodePinjaman = 'PIN-' . str_pad($lastPinNum + 1, 5, '0', STR_PAD_LEFT);

        // Hitung angsuran per bulan (pokok + margin)
        $totalBayar       = $request->jumlah_pinjaman * (1 + $request->margin / 100);
        $angsuranPerBulan = round($totalBayar / $request->tenor_bulan);

        $pinjaman = Pinjaman::create([
            'anggota_id'         => $request->anggota_id,
            'kode_pinjaman'      => $kodePinjaman,
            'jumlah_pinjaman'    => $request->jumlah_pinjaman,
            'margin'             => $request->margin,
            'tenor_bulan'        => $request->tenor_bulan,
            'angsuran_per_bulan' => $angsuranPerBulan,
            'tgl_pengajuan'      => $request->tgl_pengajuan,
            'status'             => 'Pengajuan',
        ]);

        return response()->json($pinjaman->load('anggota'), 201);
    }

    public function show($id)
    {
        return response()->json(
            Pinjaman::with(['anggota', 'angsuranList'])->findOrFail($id)
        );
    }

    // Setujui pinjaman → otomatis generate jadwal angsuran
    public function setujui($id)
    {
        $pinjaman = Pinjaman::findOrFail($id);

        if ($pinjaman->status !== 'Pengajuan') {
            return response()->json(['message' => 'Pinjaman sudah diproses sebelumnya'], 422);
        }

        $pinjaman->update([
            'status'       => 'Disetujui',
            'tgl_disetujui'=> now()->toDateString(),
        ]);

        // Generate jadwal angsuran otomatis
        $tglMulai = Carbon::parse($pinjaman->tgl_disetujui)->addMonth();
        for ($i = 1; $i <= $pinjaman->tenor_bulan; $i++) {
            Angsuran::create([
                'pinjaman_id' => $pinjaman->id,
                'ke'          => $i,
                'jumlah'      => $pinjaman->angsuran_per_bulan,
                'status'      => 'Belum Bayar',
                'jatuh_tempo' => $tglMulai->copy()->addMonths($i - 1)->toDateString(),
            ]);
        }

        // Catat transaksi pencairan
        $lastTrx  = Transaksi::orderByRaw('CAST(SUBSTRING(kode_transaksi, 5) AS UNSIGNED) DESC')->first();
        $lastTrxN = $lastTrx ? (int) substr($lastTrx->kode_transaksi, 4) : 0;
        Transaksi::create([
            'anggota_id'      => $pinjaman->anggota_id,
            'kode_transaksi'  => 'TRX-' . str_pad($lastTrxN + 1, 6, '0', STR_PAD_LEFT),
            'jenis'           => 'Pinjaman',
            'jumlah'          => $pinjaman->jumlah_pinjaman,
            'tipe'            => 'Kredit',
            'keterangan'      => 'Pencairan ' . $pinjaman->kode_pinjaman,
            'tanggal'         => now()->toDateString(),
        ]);

        return response()->json([
            'message'  => 'Pinjaman berhasil disetujui dan jadwal angsuran telah dibuat',
            'pinjaman' => $pinjaman->load(['anggota', 'angsuranList']),
        ]);
    }

    // Tolak pinjaman
    public function tolak(Request $request, $id)
    {
        $pinjaman = Pinjaman::findOrFail($id);
        $pinjaman->update(['status' => 'Ditolak']);
        return response()->json(['message' => 'Pinjaman berhasil ditolak']);
    }

    public function update(Request $request, $id)
    {
        $pinjaman = Pinjaman::findOrFail($id);
        $pinjaman->update($request->all());
        return response()->json($pinjaman);
    }

    public function destroy($id)
    {
        Pinjaman::findOrFail($id)->delete();
        return response()->json(['message' => 'Data pinjaman berhasil dihapus']);
    }
}
