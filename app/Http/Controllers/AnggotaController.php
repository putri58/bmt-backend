<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    // Ambil semua anggota
    public function index()
    {
        return response()->json(Anggota::orderBy('created_at', 'desc')->get());
    }

    // Tambah anggota baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_anggota'       => 'required|unique:anggota',
            'nama_anggota'     => 'required|string',
            'no_identitas'     => 'required|unique:anggota',
            'jenis_identitas'  => 'required|string',
            'tempat_lahir'     => 'required|string',
            'tanggal_lahir'    => 'required|date',
            'jenis_kelamin'    => 'required|in:Pria,Wanita',
            'status_perkawinan'=> 'required|in:Menikah,Lajang,Cerai',
            'nama_ibu_kandung' => 'required|string',
            'alamat_rumah'     => 'required|string',
            'kota'             => 'required|string',
            'kode_pos'         => 'required|string',
            'email'            => 'required|email|unique:anggota',
            'no_telp'          => 'required|string',
        ]);

        $anggota = Anggota::create($validated);
        return response()->json($anggota, 201);
    }

    // Detail satu anggota
    public function show($id)
    {
        $anggota = Anggota::findOrFail($id);
        return response()->json($anggota);
    }

    // Update data anggota
    public function update(Request $request, $id)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->update($request->all());
        return response()->json($anggota);
    }

    // Hapus anggota
    public function destroy($id)
    {
        Anggota::findOrFail($id)->delete();
        return response()->json(['message' => 'Anggota berhasil dihapus']);
    }
}
