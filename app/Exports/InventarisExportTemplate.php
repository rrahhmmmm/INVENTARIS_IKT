<?php

namespace App\Exports;

use App\Models\M_perangkat;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventarisExportTemplate implements WithHeadings, ShouldAutoSize
{
    protected $perangkatId;
    protected $perangkat;

    public function __construct($perangkatId = 1)
    {
        $this->perangkatId = $perangkatId;
        $this->perangkat = M_perangkat::find($perangkatId);
    }

    public function headings(): array
    {
        // Mandatory fields (same for all device types)
        $headers = [
            'ID_MERK',
            'TIPE',
            'LOKASI_POSISI',
            'TAHUN_PENGADAAN',
            'ID_KONDISI',
            'ID_ANGGARAN',
        ];

        // Add param columns with their labels for reference
        if ($this->perangkat) {
            for ($i = 1; $i <= 16; $i++) {
                $fieldName = $this->perangkat->{"param$i"};
                if (!empty($fieldName)) {
                    // Column name is param1, param2, etc. with label in parentheses
                    $headers[] = "param{$i} ({$fieldName})";
                }
            }
        }

        return $headers;
    }
}
