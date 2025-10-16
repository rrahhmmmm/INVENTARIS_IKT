<?php

namespace App\Exports;

use App\Models\M_indeks;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IndeksExport implements FromCollection,ShouldAutoSize,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_indeks::all();
    }

    public function headings(): array
    {
        return [
            'NOMOR INDEKS',
            'WILAYAH',
            'NAMA INDEKS',
            'TANGGAL MULAI',
            'TANGGAL SELESAI',
            'DIBUAT OLEH'
        ];
    }
}
