<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InstalExportTemplate implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'NAMA_INSTAL'
        ];
    }
}
