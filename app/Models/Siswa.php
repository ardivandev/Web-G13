<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Peminjaman;

class Siswa extends Model
{
    protected $table = 'tbl_siswa';
    protected $primaryKey = 'id_siswa';
    protected $fillable = ['nama_siswa', 'nis', 'kelas'];
    public $timestamps = false;

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_siswa', 'id_siswa');
    }
}
