<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $table = 'tbl_pengembalian';
    protected $primaryKey = 'id_kembali';
    public $timestamps = false; // Aktifkan timestamps

    protected $fillable = [
        'id_pinjam',
        'tanggal_pengembalian',
        'tanggal_harus_kembali',
        'sanksi'
    ];

    protected $dates = [
        'tanggal_pengembalian',
        'tanggal_harus_kembali'
    ];

    // Relasi ke tabel peminjaman
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_pinjam', 'id_pinjam');
    }
}
