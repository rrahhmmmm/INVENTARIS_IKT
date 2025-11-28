<?php

namespace App\Imports;

use App\Models\M_subdivisi;
use App\Models\M_divisi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubdivisiImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $divisi = M_divisi::where('NAMA_DIVISI', $row['divisi'] ?? '')->first();

        if (!$divisi) {
            return null;
        }

        return new M_subdivisi([
            'ID_DIVISI'      => $divisi->ID_DIVISI,
            'NAMA_SUBDIVISI' => $row['nama_subdivisi'] ?? null,
            'CREATE_BY'      => auth()->user()->username ?? 'system'
        ]);
    }
}
