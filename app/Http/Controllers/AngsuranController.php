<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use Illuminate\Http\Request;

class AngsuranController extends Controller
{
    public function index()
    {
        return response()->json(
            Angsuran::with('pinjaman')->orderBy('created_at', 'desc')->get()
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
