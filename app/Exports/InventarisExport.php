<?php

namespace App\Exports;

use App\Models\T_inventaris;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventarisExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $terminalId;

    public function __construct($terminalId = null)
    {
        $this->terminalId = $terminalId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = T_inventaris::with(['merk', 'kondisi', 'instal', 'anggaran']);

        if ($this->terminalId) {
            $query->where('ID_TERMINAL', $this->terminalId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'MODEL',
            'TIPE',
            'SERIAL NUMBER',
            'TAHUN PENGADAAN',
            'KAPASITAS PROSESSOR',
            'MEMORI UTAMA',
            'KAPASITAS PENYIMPANAN',
            'SISTEM OPERASI',
            'USER',
            'LOKASI/POSISI',
            'KONDISI',
            'KETERANGAN',
            'TERINSTALL AV',
            'MATA ANGGARAN',
            'KETERANGAN ASSET',
            'DIBUAT OLEH'
        ];
    }

    public function map($inventaris): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $inventaris->merk->NAMA_MERK ?? '-',
            $inventaris->TIPE ?? '-',
            $inventaris->SERIAL_NUMBER ?? '-',
            $inventaris->TAHUN_PENGADAAN ?? '-',
            $inventaris->KAPASITAS_PROSESSOR ?? '-',
            $inventaris->MEMORI_UTAMA ?? '-',
            $inventaris->KAPASITAS_PENYIMPANAN ?? '-',
            $inventaris->SISTEM_OPERASI ?? '-',
            $inventaris->USER_PENANGGUNG ?? '-',
            $inventaris->LOKASI_POSISI ?? '-',
            $inventaris->kondisi->NAMA_KONDISI ?? '-',
            $inventaris->KETERANGAN ?? '-',
            $inventaris->instal->NAMA_INSTAL ?? '-',
            $inventaris->anggaran->NAMA_ANGGARAN ?? '-',
            $inventaris->KETERANGAN_ASSET ?? '-',
            $inventaris->CREATE_BY ?? '-'
        ];
    }
}
