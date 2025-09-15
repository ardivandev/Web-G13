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
        Schema::create('tbl_siswa', function (Blueprint $table) {
        $table->id('id_siswa');
        $table->string('nama_siswa', 100);
        $table->string('nis', 50)->unique();
        $table->string('kelas', 50);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
