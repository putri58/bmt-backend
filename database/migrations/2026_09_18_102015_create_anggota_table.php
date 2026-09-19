<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('id_anggota')->unique();           // AGT-001
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
            $table->decimal('simpanan', 15, 2)->default(0);
            $table->enum('status_anggota', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->date('tgl_daftar')->nullable();
            $table->date('tgl_disetujui')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
