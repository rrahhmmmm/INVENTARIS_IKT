<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KlasifikasiExportTemplate implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * Contoh template kosong untuk user isi
     */
    public function collection()
    {
        // Data kosong sebagai contoh baris pertama
        return new Collection([
            [
                'kode_klasifikasi' => '',
                'kategori'         => '',
                'deskripsi'        => '',
                'start_date'       => '',
                'end_date'         => ''
            ]
        ]);
    }

    /**
     * Header kolom yang harus diisi user
     */
    public function headings(): array
    {
        return [
            'kode_klasifikasi',
            'kategori',
            'deskripsi',
            'start_date',
            'end_date'
        ];
    }
}
