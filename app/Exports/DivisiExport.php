<?php

namespace App\Exports;

use App\Models\M_divisi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DivisiExport implements FromCollection,ShouldAutoSize,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_divisi::all();
    }

    public function headings(): array
    {
        return [
            'NAMA DIVISI',
            'DIBUAT OLEH'
        ];
    }
}
