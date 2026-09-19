<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simpanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->string('kode_simpanan')->unique();
            $table->enum('jenis_simpanan', ['Wadiah', 'Mudharabah', 'Wajib', 'Pokok']);
            $table->decimal('jumlah', 15, 2);
            $table->enum('tipe', ['Setor', 'Tarik']);
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simpanan');
    }
};
