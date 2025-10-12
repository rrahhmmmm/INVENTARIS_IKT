<?php

namespace App\Imports;

use App\Models\M_indeks;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IndeksImport implements ToModel, WithHeadingRow
{
    /**
     * Setiap baris dari Excel akan diubah menjadi model M_indeks baru
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new M_indeks([
            'NO_INDEKS'   => $row['no_indeks'] ?? null,
            'WILAYAH'     => $row['wilayah'] ?? null,
            'NAMA_INDEKS' => $row['nama_indeks'] ?? null,
            'START_DATE'  => $row['start_date'] ?? null,
            'END_DATE'    => $row['end_date'] ?? null,
            'CREATE_BY'   => auth()->user()->username ?? 'system',
        ]);
    }
}
