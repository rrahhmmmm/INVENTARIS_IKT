<?php

namespace App\Exports;

use App\Models\M_role;
use Maatwebsite\Excel\Concerns\FromCollection;

class RoleExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_role::all();
    }
}
