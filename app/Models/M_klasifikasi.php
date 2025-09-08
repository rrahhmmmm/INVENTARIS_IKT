<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_klasifikasi extends Model
{
    use HasFactory;

    protected $table = "M_KLASIFIKASI";
    protected $primaryKey = "ID_KLASIFIKASI";

    public $timestamps = false; 

    protected $fillable = [
        'KODE_KLASIFIKASI',
        'KATEGORI',
        'DESKRIPSI',
        'START_DATE',
        'END_DATE',
        'CREATE_BY'

    ];
}
