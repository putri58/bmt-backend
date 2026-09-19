<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftar', function (Blueprint $table) {
            $table->id();
            $table->string('pendaftar_id')->unique();         // PD-001
            $table->string('nama_anggota');
            $table->string('no_identitas')->unique();
            $table->string('jenis_identitas')->default('KTP');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Pria', 'Wanita']);
            $table->enum('status_perkawinan', ['Menikah', 'Lajang', 'Cerai']);
            $table->string('kewarganegaraan')->default('WNI');
            $table->string('nama_ibu_kandung');
            $table->text('alamat_rumah');
            $table->string('kota');
            $table->string('kode_pos');
            $table->string('email')->unique();
            $table->string('no_telp');
            $table->enum('status_pendaftaran', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            $table->text('alasan_tolak')->nullable();
            $table->string('id_anggota')->nullable();         // diisi setelah disetujui
            $table->date('tgl_daftar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftar');
    }
};
