<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_parameter extends Model
{
    use HasFactory;

    protected $table = "M_PARAMETER";
    protected $primaryKey = "ID_PARAMETER";

    public $timestamps = false;

    protected $fillable = [
        'Nilai_parameter',
        'keterangan',
        'create_by'
    ];
}
