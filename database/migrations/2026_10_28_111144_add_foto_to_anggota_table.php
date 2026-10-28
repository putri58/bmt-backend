<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->string('foto_identitas')->nullable()->after('no_telp');
            $table->string('pas_foto')->nullable()->after('foto_identitas');
            $table->string('foto_npwp')->nullable()->after('pas_foto');
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['foto_identitas', 'pas_foto', 'foto_npwp']);
        });
    }
};
