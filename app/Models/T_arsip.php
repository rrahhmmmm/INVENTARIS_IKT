<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class T_arsip extends Model
{
    protected $table = 'T_ARSIP';
    protected $primaryKey = 'ID_ARSIP';
    public $timestamps = false;

    protected $fillable = [
        'ID_DIVISI',
        'ID_SUBDIVISI',
        'NO_INDEKS',
        'NO_BERKAS',
        'JUDUL_BERKAS',
        'NO_ISI_BERKAS',
        'JENIS_ARSIP',
        'KODE_KLASIFIKASI',
        'NO_NOTA_DINAS',
        'TANGGAL_BERKAS',
        'PERIHAL',
        'TINGKAT_PENGEMBANGAN',
        'KONDISI',
        'RAK_BAK_URUTAN',
        'KETERANGAN_SIMPAN',
        'TIPE_RETENSI',
        'TANGGAL_RETENSI',
        'MASA_INAKTIF',
        'TANGGAL_INAKTIF',
        'KETERANGAN_INAKTIF',
        'KETERANGAN',
        'FILE',
        'CREATE_BY',
        'CREATE_DATE',
        'UPDATE_BY',
        'KETERANGAN_UPDATE',
        'UPDATE_DATE',
        'STATUS',
    ];

    public function divisi()
    {
        return $this->belongsTo(M_divisi::class, 'ID_DIVISI', 'ID_DIVISI');
    }

    public function subdivisi()
    {
        return $this->belongsTo(M_subdivisi::class, 'ID_SUBDIVISI', 'ID_SUBDIVISI');
    }

}
