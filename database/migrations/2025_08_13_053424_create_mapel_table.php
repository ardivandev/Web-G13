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
      Schema::create('tbl_mapel', function (Blueprint $table) {
        $table->id('id_mapel');
        $table->string('nama_mapel', 100);
        $table->string('kode_mapel', 50)->unique();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapel');
    }
};
