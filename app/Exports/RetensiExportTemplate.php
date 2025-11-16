<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class RetensiExportTemplate implements WithHeadings,ShouldAutoSize
{
    
    public function headings(): array
    {
        return [
            'JENIS ARSIP',
            'BIDANG ARSIP',
            'TIPE ARSIP',
            'DETAIL TIPE ARSIP',
            'MASA AKTIF',
            'DESKRIPSI AKTIF',
            'MASA INAKTIF',
            'DESKRIPSI INAKTIF',
            'KETERANGAN'
        ];
    }
}
