<?php

namespace App\Exports;

use App\Models\M_indeks;
use Maatwebsite\Excel\Concerns\FromCollection;

class IndeksExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_indeks::all();
    }
}
