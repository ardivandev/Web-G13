<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPeminjaman extends Model
{
    protected $table = 'tbl_detail_pinjam';
    public $timestamps = false;

    protected $fillable = [
        'id_pinjam',
        'id_barang',
        'jumlah', // TAMBAHKAN INI - FIELD YANG HILANG!
    ];

    // Relasi ke peminjaman
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_pinjam', 'id_pinjam');
    }

    // Relasi ke barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
