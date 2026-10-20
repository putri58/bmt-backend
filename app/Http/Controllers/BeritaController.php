<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BeritaController extends Controller
{
    // Daftar berita untuk pengunjung
    public function index()
    {
        $berita = Berita::where('status', 'published')
            ->latest()
            ->get();

        return response()->json($berita);
    }

    // Detail berita untuk pengunjung
    public function show(string $id)
    {
        $berita = Berita::where('status', 'published')
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                    ->orWhere('slug', $id);
            })
            ->firstOrFail();

        return response()->json($berita);
    }

    // Daftar seluruh berita untuk admin
    public function adminIndex(Request $request)
    {
        if (!in_array($request->user()->role, ['admin', 'superadmin'])) {
            return response()->json([
                'message' => 'Kamu tidak memiliki akses.'
            ], 403);
        }

        return response()->json(
            Berita::with('penulis:id,nama')
                ->latest()
                ->get()
        );
    }

    // Tambah berita
    public function store(Request $request)
    {
        if (!in_array($request->user()->role, ['admin', 'superadmin'])) {
            return response()->json([
                'message' => 'Kamu tidak memiliki akses.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'ringkasan' => 'nullable|string',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data berita tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')
                ->store('berita', 'public');
        } else {
            unset($data['gambar']);
        }

        $data['penulis_id'] = $request->user()->id;

        $berita = Berita::create($data);

        return response()->json([
            'message' => 'Berita berhasil ditambahkan.',
            'data' => $berita
        ], 201);
    }

    // Ubah berita
    public function update(Request $request, string $id)
    {
        if (!in_array($request->user()->role, ['admin', 'superadmin'])) {
            return response()->json([
                'message' => 'Kamu tidak memiliki akses.'
            ], 403);
        }

        $berita = Berita::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'ringkasan' => 'nullable|string',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data berita tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('gambar')) {
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }

            $data['gambar'] = $request->file('gambar')
                ->store('berita', 'public');
        } else {
            unset($data['gambar']);
        }

        $berita->update($data);

        return response()->json([
            'message' => 'Berita berhasil diperbarui.',
            'data' => $berita
        ]);
    }

    // Hapus berita
    public function destroy(Request $request, string $id)
    {
        if (!in_array($request->user()->role, ['admin', 'superadmin'])) {
            return response()->json([
                'message' => 'Kamu tidak memiliki akses.'
            ], 403);
        }

        $berita = Berita::findOrFail($id);

        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->delete();

        return response()->json([
            'message' => 'Berita berhasil dihapus.'
        ]);
    }
}
