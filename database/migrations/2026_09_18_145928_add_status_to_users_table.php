<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Status akun: aktif = bisa login, pending = menunggu persetujuan admin
            $table->enum('status', ['aktif', 'pending'])->default('aktif')->after('role');
            // Relasi ke tabel pendaftar (untuk tahu pendaftar mana yang punya akun ini)
            $table->string('pendaftar_id')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'pendaftar_id']);
        });
    }
};
