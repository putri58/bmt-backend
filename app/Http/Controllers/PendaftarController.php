<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pendaftar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class PendaftarController extends Controller
{
    private function uploadToCloudinary($file, string $folder): string
    {
        $cloudinary = new Cloudinary(
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => ['secure' => true],
            ])
        );

        $result = $cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            ['folder' => $folder, 'resource_type' => 'auto']
        );

        return $result['secure_url'];
    }

    // Ambil pendaftar — filter by kantor jika bukan superadmin
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Pendaftar::orderBy('created_at', 'desc');

        if ($user->role !== 'superadmin' && $user->kantor !== null) {
            $query->where('kantor_tujuan', $user->kantor);
        }

        return response()->json($query->get());
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
            'kantor_tujuan'    => 'required|in:pusat,rumbai,panam,duri,cibubur',
            'foto_identitas'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'foto_npwp'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'pas_foto'         => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $fotoIdentitasPath = $this->uploadToCloudinary(
            $request->file('foto_identitas'), 'bmt/identitas'
        );

        $fotoNpwpPath = null;
        if ($request->hasFile('foto_npwp')) {
            $fotoNpwpPath = $this->uploadToCloudinary(
                $request->file('foto_npwp'), 'bmt/npwp'
            );
        }

        $pasFotoPath = $this->uploadToCloudinary(
            $request->file('pas_foto'), 'bmt/pas_foto'
        );

        $lastPD  = Pendaftar::orderByRaw('CAST(SUBSTRING(pendaftar_id, 4) AS UNSIGNED) DESC')->first();
        $lastNum = $lastPD ? (int) substr($lastPD->pendaftar_id, 3) : 0;
        $newId   = 'PD-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);

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
            'kantor_tujuan'      => $request->kantor_tujuan,
            'foto_identitas'     => $fotoIdentitasPath,
            'foto_npwp'          => $fotoNpwpPath,
            'pas_foto'           => $pasFotoPath,
            'tgl_daftar'         => now()->toDateString(),
            'status_pendaftaran' => 'Menunggu',
        ]);

        return response()->json([
            'message'      => 'Pendaftaran berhasil dikirim',
            'pendaftar_id' => $newId,
            'pendaftar'    => $pendaftar,
        ], 201);
    }

    // Setujui pendaftar → pindah ke tabel anggota
    public function setujui($id)
    {
        $pendaftar = Pendaftar::findOrFail($id);

        $lastAGT    = Anggota::orderByRaw('CAST(SUBSTRING(id_anggota, 5) AS UNSIGNED) DESC')->first();
        $lastAgtNum = $lastAGT ? (int) substr($lastAGT->id_anggota, 4) : 0;
        $agtId      = 'AGT-' . str_pad($lastAgtNum + 1, 3, '0', STR_PAD_LEFT);

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

        $pendaftar->update([
            'status_pendaftaran' => 'Disetujui',
            'id_anggota'         => $agtId,
        ]);

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
