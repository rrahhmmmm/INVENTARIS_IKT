<?php

namespace App\Imports;

use App\Models\M_terminal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TerminalImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new M_terminal([
            'KODE_TERMINAL' => $row['kode_terminal'] ?? null,
            'NAMA_TERMINAL' => $row['nama_terminal'] ?? null,
            'LOKASI'        => $row['lokasi'] ?? null,
            'CREATE_BY'     => auth()->user()->username ?? 'system',
            'UPDATE_BY'     => auth()->user()->username ?? 'system',
        ]);
    }
}
