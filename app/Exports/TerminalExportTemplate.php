<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class TerminalExportTemplate implements WithHeadings
{
    public function headings(): array
    {
        return [
            'KODE_TERMINAL',
            'NAMA_TERMINAL',
            'LOKASI',
            'CREATE_BY'
        ];
    }
}
