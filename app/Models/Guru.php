<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'tbl_guru';
    protected $primaryKey = 'id_guru';
    protected $fillable = ['nama_guru', 'nip', 'mapel'];
    public $timestamps = false;
}
