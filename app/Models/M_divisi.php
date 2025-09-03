<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_divisi extends Model
{
    use HasFactory;

    protected $table = "M_DIVISI";
    protected $primaryKey = 'ID_DIVISI';  

    public $timestamps = false;

    protected $fillable = [
        'NAMA_DIVISI',
        'CREATE_BY'

    ];
}
