<?php

namespace App\Exports;

use App\Models\M_terminal;
use Maatwebsite\Excel\Concerns\FromCollection;

class TerminalExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return M_terminal::all();
    }
}
