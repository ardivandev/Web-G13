<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'tbl_mapel';
    protected $primaryKey = 'id_mapel';
    public $timestamps = false;

    protected $fillable = [
        'nama_mapel',
        'kode_mapel'
    ];

    // Relasi ke peminjaman
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_mapel', 'id_mapel');
    }
}
