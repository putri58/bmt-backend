<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        return response()->json(
            Transaksi::with('anggota')->orderBy('created_at', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'jenis'      => 'required|in:Simpanan,Pinjaman,Angsuran',
            'jumlah'     => 'required|numeric|min:1',
            'tipe'       => 'required|in:Kredit,Debit',
            'tanggal'    => 'required|date',
        ]);

        $lastTrx       = Transaksi::orderByRaw('CAST(SUBSTRING(kode_transaksi, 5) AS UNSIGNED) DESC')->first();
        $lastTrxNum    = $lastTrx ? (int) substr($lastTrx->kode_transaksi, 4) : 0;
        $kodeTransaksi = 'TRX-' . str_pad($lastTrxNum + 1, 6, '0', STR_PAD_LEFT);

        $transaksi = Transaksi::create([
            ...$request->all(),
            'kode_transaksi' => $kodeTransaksi,
        ]);

        return response()->json($transaksi, 201);
    }

    public function show($id)
    {
        return response()->json(Transaksi::with('anggota')->findOrFail($id));
    }

    public function destroy($id)
    {
        Transaksi::findOrFail($id)->delete();
        return response()->json(['message' => 'Data transaksi berhasil dihapus']);
    }
}
