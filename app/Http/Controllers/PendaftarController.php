<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pendaftar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PendaftarController extends Controller
{
    // Ambil semua pendaftar
    public function index()
    {
        return response()->json(Pendaftar::orderBy('created_at', 'desc')->get());
    }

    // Pendaftaran baru dari halaman guest
    public function store(Request $request)
    {
        $request->validate([
            'nama_anggota'     => 'required|string',
            'no_identitas'     => 'required|unique:pendaftar',
            'jenis_identitas'  => 'required|string',
            'tempat_lahir'     => 'required|string',
            'tanggal_lahir'    => 'required|date',
            'jenis_kelamin'    => 'required|in:Pria,Wanita',
            'status_perkawinan'=> 'required|in:Menikah,Lajang,Cerai',
            'nama_ibu_kandung' => 'required|string',
            'alamat_rumah'     => 'required|string',
            'kota'             => 'required|string',
            'kode_pos'         => 'required|string',
            'email'            => 'required|email|unique:pendaftar|unique:users',
            'no_telp'          => 'required|string',
            'username'         => 'required|unique:users|min:4',
            'password'         => 'required|min:8',
            'foto_identitas'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'foto_npwp'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Simpan foto identitas
        $fotoIdentitasPath = $request->file('foto_identitas')
            ->store('dokumen/identitas', 'public');

        // Simpan foto NPWP kalau ada
        $fotoNpwpPath = null;
        if ($request->hasFile('foto_npwp')) {
            $fotoNpwpPath = $request->file('foto_npwp')
                ->store('dokumen/npwp', 'public');
        }

        // Generate ID pendaftar
        $count = Pendaftar::count() + 1;
        $newId = 'PD-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Simpan data pendaftar
        $pendaftar = Pendaftar::create([
            'pendaftar_id'       => $newId,
            'nama_anggota'       => $request->nama_anggota,
            'no_identitas'       => $request->no_identitas,
            'jenis_identitas'    => $request->jenis_identitas,
            'tempat_lahir'       => $request->tempat_lahir,
            'tanggal_lahir'      => $request->tanggal_lahir,
            'kewarganegaraan'    => $request->kewarganegaraan ?? 'WNI',
            'jenis_kelamin'      => $request->jenis_kelamin,
            'status_perkawinan'  => $request->status_perkawinan,
            'nama_ibu_kandung'   => $request->nama_ibu_kandung,
            'alamat_rumah'       => $request->alamat_rumah,
            'kota'               => $request->kota,
            'kode_pos'           => $request->kode_pos,
            'email'              => $request->email,
            'no_telp'            => $request->no_telp,
            'foto_identitas'     => $fotoIdentitasPath,
            'foto_npwp'          => $fotoNpwpPath,
            'tgl_daftar'         => now()->toDateString(),
            'status_pendaftaran' => 'Menunggu',
        ]);

        // Buat akun user dengan status pending
        User::create([
            'nama'         => $request->nama_anggota,
            'username'     => $request->username,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => 'anggota',
            'status'       => 'pending',
            'pendaftar_id' => $newId,
        ]);

        return response()->json($pendaftar, 201);
    }

    // Setujui pendaftar → pindah ke tabel anggota + aktifkan akun
    public function setujui($id)
    {
        $pendaftar = Pendaftar::findOrFail($id);

        // Generate ID anggota
        $count = Anggota::count() + 1;
        $agtId = 'AGT-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Buat record anggota baru
        $anggota = Anggota::create([
            'id_anggota'       => $agtId,
            'nama_anggota'     => $pendaftar->nama_anggota,
            'no_identitas'     => $pendaftar->no_identitas,
            'jenis_identitas'  => $pendaftar->jenis_identitas,
            'tempat_lahir'     => $pendaftar->tempat_lahir,
            'tanggal_lahir'    => $pendaftar->tanggal_lahir,
            'jenis_kelamin'    => $pendaftar->jenis_kelamin,
            'status_perkawinan'=> $pendaftar->status_perkawinan,
            'kewarganegaraan'  => $pendaftar->kewarganegaraan,
            'nama_ibu_kandung' => $pendaftar->nama_ibu_kandung,
            'alamat_rumah'     => $pendaftar->alamat_rumah,
            'kota'             => $pendaftar->kota,
            'kode_pos'         => $pendaftar->kode_pos,
            'email'            => $pendaftar->email,
            'no_telp'          => $pendaftar->no_telp,
            'simpanan'         => 0,
            'status_anggota'   => 'Aktif',
            'tgl_daftar'       => $pendaftar->tgl_daftar,
            'tgl_disetujui'    => now()->toDateString(),
        ]);

        // Update status pendaftar
        $pendaftar->update([
            'status_pendaftaran' => 'Disetujui',
            'id_anggota'         => $agtId,
        ]);

        // Aktifkan akun user dan hubungkan ke anggota
        User::where('pendaftar_id', $pendaftar->pendaftar_id)
            ->update(['status' => 'aktif', 'anggota_id' => $agtId]);

        return response()->json([
            'message' => 'Pendaftar berhasil disetujui',
            'anggota' => $anggota,
        ]);
    }

    // Tolak pendaftar
    public function tolak(Request $request, $id)
    {
        $pendaftar = Pendaftar::findOrFail($id);
        $pendaftar->update([
            'status_pendaftaran' => 'Ditolak',
            'alasan_tolak'       => $request->alasan ?? '',
        ]);

        // Hapus akun user yang pending
        User::where('pendaftar_id', $pendaftar->pendaftar_id)->delete();

        return response()->json(['message' => 'Pendaftar berhasil ditolak']);
    }

    // Hapus pendaftar
    public function destroy($id)
    {
        Pendaftar::findOrFail($id)->delete();
        return response()->json(['message' => 'Data pendaftar berhasil dihapus']);
    }
}
