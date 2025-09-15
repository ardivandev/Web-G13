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
      Schema::create('tbl_barang', function (Blueprint $table) {
        $table->id('id_barang');
        $table->string('nama_barang', 150);
        $table->text('spesifikasi')->nullable();
        $table->integer('stok')->default(0);
        $table->unsignedBigInteger('id_kategori');

        $table->foreign('id_kategori')
              ->references('id_kategori')
              ->on('tbl_kategori_barang')
              ->onDelete('cascade');
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
