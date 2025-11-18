<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_jenisnaskah extends Model
{
    use HasFactory;

    protected $table = "M_JENISNASKAH";
    protected $primaryKey = "ID_JENISNASKAH";
    
    public $timestamps = false;

    protected $fillable = [
        'NAMA_JENIS',
        'CREATE_BY'
    ];
}
