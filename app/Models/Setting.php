<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
  use HasFactory;
  protected $table = 'tbl_settings';
  protected $fillable = ['status_gudang'];
}
