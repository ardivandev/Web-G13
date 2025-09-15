<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'tbl_admin';
    protected $fillable = ['email', 'password'];

    protected $hidden = ['password'];
}
