<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_instal extends Model
{
    use HasFactory;

    protected $table = "M_INSTAL";
    protected $primaryKey = 'ID_INSTAL';

    public $timestamps = false;

    protected $fillable = [
        'NAMA_INSTAL',
        'CREATE_BY'
    ];
}
