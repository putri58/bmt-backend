<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom kantor ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('kantor', ['pusat', 'rumbai', 'panam', 'duri', 'cibubur'])
                  ->nullable()
                  ->after('role')
                  ->comment('Kantor cabang yang dikelola admin ini. Null = superadmin');
        });

        // Tambah kolom kantor_tujuan ke tabel pendaftar
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->enum('kantor_tujuan', ['pusat', 'rumbai', 'panam', 'duri', 'cibubur'])
                  ->default('pusat')
                  ->after('tgl_daftar')
                  ->comment('Kantor tujuan yang dipilih pendaftar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kantor');
        });

        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropColumn('kantor_tujuan');
        });
    }
};
