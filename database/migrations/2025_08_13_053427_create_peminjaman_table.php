<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_peminjaman', function (Blueprint $table) {
        $table->id('id_pinjam');
        $table->unsignedBigInteger('id_siswa')->nullable();
        $table->unsignedBigInteger('id_guru')->nullable();
        $table->unsignedBigInteger('id_mapel')->nullable();
        $table->unsignedBigInteger('id_petugas');
        $table->unsignedBigInteger('id_ruangan')->nullable();
        $table->string('no_telp', 20)->nullable();
        $table->time('mulai_kbm');
        $table->time('selesai_kbm');
        $table->string('jaminan', 100)->nullable();

        $table->foreign('id_siswa')->references('id_siswa')->on('tbl_siswa')->onDelete('set null');
        $table->foreign('id_guru')->references('id_guru')->on('tbl_guru')->onDelete('set null');
        $table->foreign('id_mapel')->references('id_mapel')->on('tbl_mapel')->onDelete('set null');
        $table->foreign('id_petugas')->references('id_petugas')->on('tbl_petugas')->onDelete('cascade');
        $table->foreign('id_ruangan')->references('id_ruangan')->on('tbl_ruangan')->onDelete('set null');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
