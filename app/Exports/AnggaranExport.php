<?php

namespace App\Exports;

use App\Models\M_anggaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AnggaranExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return M_anggaran::all();
    }

    public function headings(): array
    {
        return [
            'NAMA ANGGARAN',
            'DIBUAT OLEH'
        ];
    }
}
