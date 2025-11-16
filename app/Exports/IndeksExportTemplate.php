<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IndeksExportTemplate implements WithHeadings,ShouldAutoSize
{
    /**
 
    */
    public function headings(): array
    {
        return [
            'NO_INDEKS',
            'WILAYAH',
            'NAMA_INDEKS',
            'START_DATE',
            'END_DATE'
        ];
    }
}
