<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_lokasi extends Model
{
    use HasFactory;
    
    protected $table = "M_LOKASI";
    protected $primaryKey = "ID_LOKASI";

    public $timestamps = false;
    
    protected $fillable = [
        'NAMA_LOKASI',
        'ALAMAT',
        'create_by',
    ];

}
