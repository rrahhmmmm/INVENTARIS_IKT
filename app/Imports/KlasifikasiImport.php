<?php

namespace App\Imports;

use App\Models\M_klasifikasi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KlasifikasiImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new M_klasifikasi([
            'KODE_KLASIFIKASI' => $row['kode_klasifikasi'] ?? null,
            'KATEGORI'         => $row['kategori'] ?? null,
            'DESKRIPSI'        => $row['deskripsi'] ?? null,
            'START_DATE'       => $row['start_date'] ?? null,
            'END_DATE'         => $row['end_date'] ?? null,
            'CREATE_BY'        => auth()->user()->username ?? 'system',
        ]);
    }
}
