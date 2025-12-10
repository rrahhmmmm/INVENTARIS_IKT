<?php

namespace App\Exports;

use App\Models\M_instal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InstalExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return M_instal::all();
    }

    public function headings(): array
    {
        return [
            'NAMA INSTAL',
            'DIBUAT OLEH'
        ];
    }
}
