<?php

namespace App\Exports;

use App\Models\M_divisi;
use Maatwebsite\Excel\Concerns\FromCollection;

class DivisiExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_divisi::all();
    }
}
