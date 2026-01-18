<?php

namespace App\Exports;

use App\Models\T_inventaris;
use App\Models\M_perangkat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventarisExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $terminalId;
    protected $perangkatId;
    protected $perangkat;
    protected $rowNumber = 0;

    public function __construct($terminalId = null, $perangkatId = null)
    {
        $this->terminalId = $terminalId;
        $this->perangkatId = $perangkatId;

        if ($perangkatId) {
            $this->perangkat = M_perangkat::find($perangkatId);
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = T_inventaris::with(['merk', 'kondisi', 'anggaran', 'perangkat']);

        if ($this->terminalId) {
            $query->where('ID_TERMINAL', $this->terminalId);
        }

        if ($this->perangkatId) {
            $query->where('ID_PERANGKAT', $this->perangkatId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        // Common mandatory headers
        $headers = [
            'NO',
            'JENIS PERANGKAT',
            'MERK',
            'TIPE',
            'LOKASI/POSISI',
            'TAHUN PENGADAAN',
            'KONDISI',
            'MATA ANGGARAN',
        ];

        // Add dynamic param headers based on perangkat
        if ($this->perangkat) {
            for ($i = 1; $i <= 16; $i++) {
                $fieldName = $this->perangkat->{"param$i"};
                if (!empty($fieldName)) {
                    $headers[] = strtoupper($fieldName);
                }
            }
        }

        $headers[] = 'DIBUAT OLEH';
        return $headers;
    }

    public function map($inventaris): array
    {
        $this->rowNumber++;

        // Common mandatory fields
        $row = [
            $this->rowNumber,
            $inventaris->perangkat->NAMA_PERANGKAT ?? '-',
            $inventaris->merk->NAMA_MERK ?? '-',
            $inventaris->TIPE ?? '-',
            $inventaris->LOKASI_POSISI ?? '-',
            $inventaris->TAHUN_PENGADAAN ?? '-',
            $inventaris->kondisi->NAMA_KONDISI ?? '-',
            $inventaris->anggaran->NAMA_ANGGARAN ?? '-',
        ];

        // Add param values
        if ($this->perangkat) {
            for ($i = 1; $i <= 16; $i++) {
                $fieldName = $this->perangkat->{"param$i"};
                if (!empty($fieldName)) {
                    $row[] = $inventaris->{"param$i"} ?? '-';
                }
            }
        }

        $row[] = $inventaris->CREATE_BY ?? '-';
        return $row;
    }
}
