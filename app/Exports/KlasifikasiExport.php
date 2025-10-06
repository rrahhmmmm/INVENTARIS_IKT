<?php

namespace App\Exports;

use App\Models\M_klasifikasi;
use Maatwebsite\Excel\Concerns\FromCollection;

class KlasifikasiExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_klasifikasi::all();
    }
}
