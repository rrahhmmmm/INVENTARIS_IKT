<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_retensi extends Model
{
    use HasFactory;

    protected $table = "M_RETENSI";
    protected $primaryKey = "ID_RETENSI";

    public $timestamps = false;

    protected $fillable = [
        "jenis_arsip",
        "bidang_arsip",
        "tipe_arsip",
        "detail_tipe_arsip",
        "masa_aktif",
        "DESC_AKTIF",
        "masa_inaktif",
        "DESC_INAKTIF",
        "keterangan",
        "CREATE_BY"
    ];
}
