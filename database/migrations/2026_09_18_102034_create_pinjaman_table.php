<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->string('kode_pinjaman')->unique();
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->decimal('margin', 5, 2)->default(0);
            $table->integer('tenor_bulan');
            $table->decimal('angsuran_per_bulan', 15, 2);
            $table->enum('status', ['Pengajuan', 'Disetujui', 'Ditolak', 'Lunas'])->default('Pengajuan');
            $table->date('tgl_pengajuan');
            $table->date('tgl_disetujui')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};
