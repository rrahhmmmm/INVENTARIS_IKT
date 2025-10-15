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
        "JENIS_ARSIP",
        "BIDANG_ARSIP",
        "TIPE_ARSIP",
        "DETAIL_TIPE_ARSIP",
        "MASA_AKTIF",
        "DESC_AKTIF",
        "MASA_INAKTIF",
        "DESC_INAKTIF",
        "KETERANGAN",
        "CREATE_BY"
    ];
}
