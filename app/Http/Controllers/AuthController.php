<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah.'
            ], 401);
        }

        // Cek status akun
        if ($user->status === 'pending') {
            return response()->json([
                'message' => 'Akun Anda masih menunggu persetujuan admin.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'nama'       => $user->nama,
                'username'   => $user->username,
                'email'      => $user->email,
                'role'       => $user->role,
                'kantor'     => $user->kantor,
                'anggota_id' => $user->anggota_id,
            ]
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Berhasil logout']);
    }

    // Data user yang sedang login
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
