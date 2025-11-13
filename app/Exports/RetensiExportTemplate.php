<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class RetensiExportTemplate implements WithHeadings
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
