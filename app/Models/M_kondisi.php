<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_kondisi extends Model
{
    protected $table = 'M_KONDISI';
    protected $primaryKey = 'ID_KONDISI';
    public $timestamps = false;

    protected $fillable = [
        'NAMA_KONDISI',
        'CREATE_BY',
        'UPDATE_BY'
    ];
}
