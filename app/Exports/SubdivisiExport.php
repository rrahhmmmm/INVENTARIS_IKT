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
            $subdivisi->divisi->NAMA_DIVISI ?? '-',   // ambil nama divisi, fallback '-'
            $subdivisi->CREATE_BY,
        ];
    }
}
