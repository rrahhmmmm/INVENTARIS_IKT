<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_status extends Model
{
  use HasFactory;

  protected $table = "M_STATUS";
  protected $primaryKey = "ID_STATUS";

  public $timestamps = false;   

  protected $fillable = [
    'nama_status',
    'keterangan',
    'create_by'
  ];
}
