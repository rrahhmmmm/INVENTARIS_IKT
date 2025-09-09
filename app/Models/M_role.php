<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_role extends Model
{
    use HasFactory;

    protected $table = "M_ROLE";
    protected $primaryKey = "ID_ROLE";

    public $timestamps = false;

    protected $fillable = [
        'Nama_role',
        'keterangan',
        'create_by'
    ];
}
