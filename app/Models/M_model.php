<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_model extends Model
{
    use HasFactory;

    protected $table = "M_MODEL";
    protected $primaryKey = "ID_MODEL";

    public $timestamps = false;

    protected $fillable = [
        'NAMA_MODEL',
        'KETERANGAN',
        'create_by'
    ];
}
