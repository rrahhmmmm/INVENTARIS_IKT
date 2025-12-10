<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_merk extends Model
{
    use HasFactory;

    protected $table = "M_MERK";
    protected $primaryKey = 'ID_MERK';

    public $timestamps = false;

    protected $fillable = [
        'NAMA_MERK',
        'CREATE_BY'
    ];
}
