<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\Angsuran;
use Illuminate\Http\Request;

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

        $count       = Pinjaman::count() + 1;
        $kodePinjaman = 'PIN-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        // Hitung angsuran per bulan
        $totalBayar       = $request->jumlah_pinjaman * (1 + $request->margin / 100);
        $angsuranPerBulan = $totalBayar / $request->tenor_bulan;

        $pinjaman = Pinjaman::create([
            ...$request->all(),
            'kode_pinjaman'      => $kodePinjaman,
            'angsuran_per_bulan' => $angsuranPerBulan,
            'status'             => 'Pengajuan',
        ]);

        return response()->json($pinjaman, 201);
    }

    public function show($id)
    {
        return response()->json(
            Pinjaman::with(['anggota', 'angsuranList'])->findOrFail($id)
        );
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
