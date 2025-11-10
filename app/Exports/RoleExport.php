<?php

namespace App\Exports;

use App\Models\M_role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RoleExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_role::all();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return ['ID', 'Nama Role', 'Deskripsi'];
    }
}
