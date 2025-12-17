<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventarisExportTemplate implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
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
            'KETERANGAN_ASSET'
        ];
    }
}
