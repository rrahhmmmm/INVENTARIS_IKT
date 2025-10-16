<?php

namespace App\Exports;

use App\Models\M_terminal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TerminalExport implements FromCollection,ShouldAutoSize,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_terminal::all();
    }

    public function headings(): array
    {
        return [
            'Kode Terminal',
            'Nama Terminal',
            'Lokasi',
            'Dibuat Oleh',
        ];
    }
}
