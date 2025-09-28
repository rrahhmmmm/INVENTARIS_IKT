<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class DivisiExportTemplate implements WithHeadings
{
    public function headings(): array
    {
        return [
            'NAMA_DIVISI',
            'CREATE_BY'
        ];
    }
}
