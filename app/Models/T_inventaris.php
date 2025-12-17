<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class T_inventaris extends Model
{
    use HasFactory;

    protected $table = 'T_INVENTARIS';
    protected $primaryKey = 'ID_INVENTARIS';
    public $timestamps = false;

    protected $fillable = [
        'ID_TERMINAL',
        'ID_MERK',
        'TIPE',
        'SERIAL_NUMBER',
        'TAHUN_PENGADAAN',
        'KAPASITAS_PROSESSOR',
        'MEMORI_UTAMA',
        'KAPASITAS_PENYIMPANAN',
        'SISTEM_OPERASI',
        'USER_PENANGGUNG',
        'LOKASI_POSISI',
        'ID_KONDISI',
        'KETERANGAN',
        'ID_INSTAL',
        'ID_ANGGARAN',
        'KETERANGAN_ASSET',
        'CREATE_BY',
        'UPDATE_BY',
        'STATUS'
    ];

    public function terminal()
    {
        return $this->belongsTo(M_terminal::class, 'ID_TERMINAL', 'ID_TERMINAL');
    }

    public function merk()
    {
        return $this->belongsTo(M_merk::class, 'ID_MERK', 'ID_MERK');
    }

    public function kondisi()
    {
        return $this->belongsTo(M_kondisi::class, 'ID_KONDISI', 'ID_KONDISI');
    }

    public function instal()
    {
        return $this->belongsTo(M_instal::class, 'ID_INSTAL', 'ID_INSTAL');
    }

    public function anggaran()
    {
        return $this->belongsTo(M_anggaran::class, 'ID_ANGGARAN', 'ID_ANGGARAN');
    }
}
