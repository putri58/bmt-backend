<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class AngsuranController extends Controller
{
    public function index()
    {
        return response()->json(
            Angsuran::with(['pinjaman.anggota'])->orderBy('jatuh_tempo', 'asc')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'ke'          => 'required|integer|min:1',
            'jumlah'      => 'required|numeric|min:1',
            'jatuh_tempo' => 'required|date',
        ]);

        $angsuran = Angsuran::create($request->all());
        return response()->json($angsuran, 201);
    }

    // Tandai angsuran sebagai lunas
    public function lunas($id)
    {
        $angsuran = Angsuran::with('pinjaman')->findOrFail($id);

        if ($angsuran->status === 'Lunas') {
            return response()->json(['message' => 'Angsuran sudah lunas sebelumnya'], 422);
        }

        $angsuran->update([
            'status'   => 'Lunas',
            'tgl_bayar'=> now()->toDateString(),
        ]);

        // Catat transaksi pembayaran angsuran
        $lastTrx  = Transaksi::orderByRaw('CAST(SUBSTRING(kode_transaksi, 5) AS UNSIGNED) DESC')->first();
        $lastTrxN = $lastTrx ? (int) substr($lastTrx->kode_transaksi, 4) : 0;
        Transaksi::create([
            'anggota_id'     => $angsuran->pinjaman->anggota_id,
            'kode_transaksi' => 'TRX-' . str_pad($lastTrxN + 1, 6, '0', STR_PAD_LEFT),
            'jenis'          => 'Angsuran',
            'jumlah'         => $angsuran->jumlah,
            'tipe'           => 'Debit',
            'keterangan'     => 'Angsuran ke-' . $angsuran->ke . ' ' . $angsuran->pinjaman->kode_pinjaman,
            'tanggal'        => now()->toDateString(),
        ]);

        // Cek apakah semua angsuran sudah lunas → update status pinjaman
        $totalAngsuran = Angsuran::where('pinjaman_id', $angsuran->pinjaman_id)->count();
        $sudahLunas    = Angsuran::where('pinjaman_id', $angsuran->pinjaman_id)->where('status', 'Lunas')->count();

        if ($totalAngsuran === $sudahLunas) {
            Pinjaman::where('id', $angsuran->pinjaman_id)->update(['status' => 'Lunas']);
        }

        return response()->json(['message' => 'Angsuran berhasil ditandai lunas']);
    }

    public function update(Request $request, $id)
    {
        $angsuran = Angsuran::findOrFail($id);
        $angsuran->update($request->all());
        return response()->json($angsuran);
    }

    public function destroy($id)
    {
        Angsuran::findOrFail($id)->delete();
        return response()->json(['message' => 'Data angsuran berhasil dihapus']);
    }
}
