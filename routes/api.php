<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\PendaftarController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\AngsuranController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MemberController;

// ── PUBLIK (tidak perlu login) ──────────────────────────────
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/daftar', [PendaftarController::class, 'store']);

Route::get('/ping', function () {
    return response()->json(['message' => 'API berjalan!']);
});

// ── PERLU LOGIN ─────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Anggota
    Route::apiResource('anggota', AnggotaController::class);

    // Pendaftar
    Route::get('pendaftar',             [PendaftarController::class, 'index']);
    Route::post('pendaftar/{id}/setujui', [PendaftarController::class, 'setujui']);
    Route::post('pendaftar/{id}/tolak',   [PendaftarController::class, 'tolak']);
    Route::delete('pendaftar/{id}',       [PendaftarController::class, 'destroy']);

    // Simpanan, Pinjaman, Angsuran, Transaksi
    Route::apiResource('simpanan',  SimpananController::class);
    Route::apiResource('pinjaman',  PinjamanController::class);
    Route::apiResource('angsuran',  AngsuranController::class);
    Route::apiResource('transaksi', TransaksiController::class);

    // Users (untuk manajemen admin)
    Route::apiResource('users', UserController::class);

    // ── MEMBER ROUTES (data pribadi anggota yang login) ──
    Route::prefix('member')->group(function () {
        Route::get('dashboard',  [MemberController::class, 'dashboard']);
        Route::get('profil',     [MemberController::class, 'profil']);
        Route::get('simpanan',   [MemberController::class, 'simpanan']);
        Route::get('pinjaman',   [MemberController::class, 'pinjaman']);
        Route::get('angsuran',   [MemberController::class, 'angsuran']);
        Route::get('transaksi',  [MemberController::class, 'transaksi']);
    });
});
