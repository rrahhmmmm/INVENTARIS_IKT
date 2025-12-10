<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AnggaranExportTemplate implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'NAMA_ANGGARAN'
        ];
    }
}
