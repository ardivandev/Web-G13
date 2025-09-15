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
        Schema::create('tbl_detail_pinjam', function (Blueprint $table) {
        $table->unsignedBigInteger('id_pinjam');
        $table->unsignedBigInteger('id_barang');

        $table->primary(['id_pinjam', 'id_barang']);

        $table->foreign('id_pinjam')->references('id_pinjam')->on('tbl_peminjaman')->onDelete('cascade');
        $table->foreign('id_barang')->references('id_barang')->on('tbl_barang')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pinjam');
    }
};
