<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi')->unique();
            $table->foreignId('layanan_id')->constrained('layanan')->onDelete('cascade');
            $table->string('nama');
            $table->string('nik', 16);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->text('alamat');
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->string('no_hp');
            $table->string('email');
            $table->text('keperluan');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['baru', 'proses', 'selesai', 'ditolak'])->default('baru');
            $table->date('estimasi_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan');
    }
};
