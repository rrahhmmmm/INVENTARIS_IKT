<?php

namespace App\Imports;

use App\Models\M_retensi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RetensiImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Lewati baris kosong
        if (empty($row['jenis_arsip']) || empty($row['bidang_arsip']) || empty($row['tipe_arsip'])) {
            return null;
        }

        return new M_retensi([
            'JENIS_ARSIP'       => $row['jenis_arsip'] ?? null,
            'BIDANG_ARSIP'      => $row['bidang_arsip'] ?? null,
            'TIPE_ARSIP'        => $row['tipe_arsip'] ?? null,
            'DETAIL_TIPE_ARSIP' => $row['detail_tipe_arsip'] ?? null,
            'MASA_AKTIF'        => $row['masa_aktif'] ?? null,
            'DESC_AKTIF'        => $row['desc_aktif'] ?? null,
            'MASA_INAKTIF'      => $row['masa_inaktif'] ?? null,
            'DESC_INAKTIF'      => $row['desc_inaktif'] ?? null,
            'KETERANGAN'        => $row['keterangan'] ?? null,
            'CREATE_BY'         => auth()->user()->username ?? 'system',
        ]);
    }
}
