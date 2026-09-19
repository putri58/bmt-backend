<?php

namespace App\Http\Controllers;

use App\Models\Simpanan;
use App\Models\Anggota;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index()
    {
        return response()->json(
            Simpanan::with('anggota')->orderBy('created_at', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggota_id'    => 'required|exists:anggota,id',
            'jenis_simpanan'=> 'required|in:Wadiah,Mudharabah,Wajib,Pokok',
            'jumlah'        => 'required|numeric|min:1',
            'tipe'          => 'required|in:Setor,Tarik',
            'tanggal'       => 'required|date',
        ]);

        $count        = Simpanan::count() + 1;
        $kodeSimpanan = 'SMP-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        $simpanan = Simpanan::create([
            ...$request->all(),
            'kode_simpanan' => $kodeSimpanan,
        ]);

        // Update total simpanan anggota
        $anggota = Anggota::findOrFail($request->anggota_id);
        if ($request->tipe === 'Setor') {
            $anggota->increment('simpanan', $request->jumlah);
        } else {
            $anggota->decrement('simpanan', $request->jumlah);
        }

        return response()->json($simpanan, 201);
    }

    public function show($id)
    {
        return response()->json(Simpanan::with('anggota')->findOrFail($id));
    }

    public function destroy($id)
    {
        Simpanan::findOrFail($id)->delete();
        return response()->json(['message' => 'Data simpanan berhasil dihapus']);
    }
}
