<?php

namespace App\Exports;

use App\Models\M_klasifikasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KlasifikasiExport implements FromCollection,ShouldAutoSize,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_klasifikasi::all();
    }

    public function headings(): array
    {
        return [
            'KODE KLASIFIKASI',
            'KATEGORI',
            'DESKRIPSI',
            'TANGGAL MULAI',
            'TANGGAL SELESAI',
            'DIBUAT OLEH'
        ];
    }
}
