<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_terminal extends Model
{
    use HasFactory;

    protected $table = "M_TERMINAL";
    protected $primaryKey = 'ID_TERMINAL';  

    public $timestamps = false;

    protected $fillable = [
        'KODE_TERMINAL',
        'NAMA_TERMINAL',
        'LOKASI',
        'CREATE_BY'
    ];
}
