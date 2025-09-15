<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;

class Kategori extends Model {
    protected $table = 'tbl_kategori_barang';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori'
    ];

    public $timestamps = false;

    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_kategori', 'id_kategori');
    }
}
