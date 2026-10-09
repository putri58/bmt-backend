<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL enum tidak bisa di-alter langsung dengan Blueprint,
        // pakai raw query untuk mengubah enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','anggota') NOT NULL DEFAULT 'anggota'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','anggota') NOT NULL DEFAULT 'anggota'");
    }
};
