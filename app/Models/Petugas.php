<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\Model;

class Petugas extends Authenticatable
{
   use Notifiable;
    protected $table = 'tbl_petugas';
    protected $primaryKey = 'id_petugas';

    protected $fillable = [
        'nama_petugas',
        'username',
        'email',
        'password',
        'password_asli', // supaya bisa simpan password asli
        'role'
    ];

    public $timestamps = false;
}
