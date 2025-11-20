<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_tingkatpengembangan extends Model
{
    use HasFactory;

    protected $table = "M_TINGKATPENGEMBANGAN";
    protected $primaryKey = "ID_TINGKATPENGEMBANGAN";

    public $timestamps = false;

    protected $fillable = [
        'NAMA_PENGEMBANGAN',
        'CREATE_BY'
    ];
}
