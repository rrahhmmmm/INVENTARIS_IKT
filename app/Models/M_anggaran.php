<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_anggaran extends Model
{
    use HasFactory;

    protected $table = "M_ANGGARAN";
    protected $primaryKey = "ID_ANGGARAN";

    public $timestamps = false;

    protected $fillable = [
        'nama_anggaran',
        'tahun_anggaran',
        'keterangan',
        'create_by',
    ] ;
}
