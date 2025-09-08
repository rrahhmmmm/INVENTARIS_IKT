<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_indeks extends Model
{
    use HasFactory;

    protected $table = "M_INDEKS";
    protected $primaryKey = "ID_INDEKS";

    public $timestamps = false;

    protected $fillable = [
        'NO_INDEKS',
        'WILAYAH',
        'NAMA_INDEKS',
        'START_DATE',
        'END_DATE',
        'CREATE_BY'
    ];
}
