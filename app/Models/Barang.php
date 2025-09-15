<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'tbl_barang';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'nama_barang',
        'spesifikasi',
        'stok',
        'id_kategori',
        'gambar',
    ];

    public $timestamps = false;

    // Relasi: Barang dimiliki oleh satu Kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }
}
