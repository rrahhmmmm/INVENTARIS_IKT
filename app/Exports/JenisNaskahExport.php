<?php

namespace App\Exports;

use App\Models\M_jenisnaskah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JenisNaskahExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return M_jenisnaskah::select('NAMA_JENIS', 'CREATE_BY')->get();
    }

    public function headings(): array
    {
        return ['NAMA_JENIS', 'CREATE_BY'];
    }
}