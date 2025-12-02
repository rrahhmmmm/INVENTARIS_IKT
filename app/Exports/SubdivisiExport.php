<?php

namespace App\Exports;

use App\Models\M_subdivisi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SubdivisiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * Ambil data subdivisi dengan relasi divisi
     */
    public function collection()
    {
        return M_subdivisi::with('divisi')->get();
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'ID Subdivisi',
            'Nama Subdivisi',
            'Kode Lokasi',
            'Divisi',
            'Created By'
        ];
    }

    /**
     * Mapping setiap row
     */
    public function map($subdivisi): array
    {
        return [
            $subdivisi->ID_SUBDIVISI,
            $subdivisi->NAMA_SUBDIVISI,
            $subdivisi->KODE_LOKASI ?? '-',
            $subdivisi->divisi->NAMA_DIVISI ?? '-',
            $subdivisi->CREATE_BY,
        ];
    }
}
