<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JenisNaskahExportTemplate implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Contoh Jenis Naskah 1'],
            ['Contoh Jenis Naskah 2'],
        ];
    }

    public function headings(): array
    {
        return ['NAMA_JENIS'];
    }
}