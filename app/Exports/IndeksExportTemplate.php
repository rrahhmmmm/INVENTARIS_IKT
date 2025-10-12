<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class IndeksExportTemplate implements WithHeadings
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
            'END_DATE',
            'CREATE_BY'
        ];
    }
}
