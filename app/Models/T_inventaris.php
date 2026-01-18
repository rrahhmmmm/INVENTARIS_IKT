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
        'ID_PERANGKAT',
        'ID_MERK',
        'TIPE',
        'TAHUN_PENGADAAN',
        'LOKASI_POSISI',
        'ID_KONDISI',
        'ID_ANGGARAN',
        'CREATE_BY',
        'UPDATE_BY',
        'STATUS',
        'param1', 'param2', 'param3', 'param4', 'param5', 'param6',
        'param7', 'param8', 'param9', 'param10', 'param11', 'param12',
        'param13', 'param14', 'param15', 'param16'
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

    public function anggaran()
    {
        return $this->belongsTo(M_anggaran::class, 'ID_ANGGARAN', 'ID_ANGGARAN');
    }

    public function perangkat()
    {
        return $this->belongsTo(M_perangkat::class, 'ID_PERANGKAT', 'ID_PERANGKAT');
    }

    /**
     * Get all param values with their labels from perangkat
     */
    public function getParamValuesWithLabels(): array
    {
        $result = [];
        $perangkat = $this->perangkat;

        if ($perangkat) {
            for ($i = 1; $i <= 16; $i++) {
                $label = $perangkat->{"param$i"};
                if (!empty($label)) {
                    $result[] = [
                        'key' => "param$i",
                        'label' => $label,
                        'value' => $this->{"param$i"}
                    ];
                }
            }
        }

        return $result;
    }
}
