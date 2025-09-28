<?php

namespace App\Imports;

use App\Models\M_divisi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DivisiImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new M_divisi([
            'NAMA_DIVISI' => $row['nama_divisi'] ?? null,
            'CREATE_BY'     => auth()->user()->username ?? 'system'
        ]);
    }
}
