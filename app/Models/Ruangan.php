<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'tbl_ruangan';
    protected $primaryKey = 'id_ruangan';
    public $timestamps = false;

    protected $fillable = [
        'nama_ruangan'
    ];

    // Relasi ke peminjaman
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_ruangan', 'id_ruangan');
    }
}
