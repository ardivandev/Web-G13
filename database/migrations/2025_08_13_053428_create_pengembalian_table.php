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
        Schema::create('tbl_pengembalian', function (Blueprint $table) {
        $table->id('id_kembali');
        $table->unsignedBigInteger('id_pinjam');
        $table->date('tanggal_pengembalian');
        $table->date('tanggal_harus_kembali');
        $table->string('sanksi', 100)->nullable();

        $table->foreign('id_pinjam')->references('id_pinjam')->on('tbl_peminjaman')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
