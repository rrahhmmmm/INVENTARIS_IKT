<?php

namespace App\Exports;

use App\Models\M_merk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MerkExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return M_merk::all();
    }

    public function headings(): array
    {
        return [
            'NAMA MERK',
            'DIBUAT OLEH'
        ];
    }
}
