<?php

namespace App\Exports;

use App\Models\M_retensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RetensiExport implements FromCollection,ShouldAutoSize,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_retensi::all();
    }

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
            'KETERANGAN',
            'DIBUAT OLEH'
        ];
    }
}
