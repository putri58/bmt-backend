<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Ambil semua user
    public function index()
    {
        return response()->json(User::orderBy('created_at', 'desc')->get());
    }

    // Tambah user/admin baru
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string',
            'username' => 'required|unique:users|min:4',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role'     => 'required|in:superadmin,admin,anggota',
            'kantor'   => 'nullable|in:pusat,rumbai,panam,duri,cibubur',
            'status'   => 'required|in:aktif,pending,nonaktif',
        ]);

        $user = User::create([
            'nama'     => $request->nama,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'kantor'   => $request->role === 'superadmin' ? null : $request->kantor,
            'status'   => $request->status,
        ]);

        return response()->json($user, 201);
    }

    // Detail satu user
    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'     => 'sometimes|string',
            'username' => "sometimes|unique:users,username,{$id}",
            'email'    => "sometimes|email|unique:users,email,{$id}",
            'password' => 'sometimes|min:8',
            'role'     => 'sometimes|in:superadmin,admin,anggota',
            'kantor'   => 'nullable|in:pusat,rumbai,panam,duri,cibubur',
            'status'   => 'sometimes|in:aktif,pending,nonaktif',
        ]);

        $data = $request->except('password');

        // Superadmin tidak punya kantor
        if (isset($data['role']) && $data['role'] === 'superadmin') {
            $data['kantor'] = null;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return response()->json($user);
    }

    // Hapus user
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Admin berhasil dihapus']);
    }
}
