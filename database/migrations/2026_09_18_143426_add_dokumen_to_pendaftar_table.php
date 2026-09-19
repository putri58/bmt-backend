<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->string('foto_identitas')->nullable()->after('tgl_daftar');
            $table->string('foto_npwp')->nullable()->after('foto_identitas');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropColumn(['foto_identitas', 'foto_npwp']);
        });
    }
};
