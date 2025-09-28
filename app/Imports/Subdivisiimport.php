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
        // cari divisi berdasarkan nama
        $divisi = M_divisi::where('NAMA_DIVISI', $row['nama_divisi'] ?? '')->first();

        if (!$divisi) {
            // kalau divisi tidak ditemukan, bisa dilewati atau error
            return null;
        }

        return new M_subdivisi([
            'ID_DIVISI'      => $divisi->ID_DIVISI,
            'NAMA_SUBDIVISI' => $row['nama_subdivisi'] ?? null,
            'CREATE_BY'      => Auth::check() ? Auth::user()->username : 'system'
        ]);
    }
}
