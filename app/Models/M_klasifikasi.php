<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_klasifikasi extends Model
{
    protected $table = 'M_KLASIFIKASI';   // pastikan sesuai dengan DB
    protected $primaryKey = 'ID_KLASIFIKASI';
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
