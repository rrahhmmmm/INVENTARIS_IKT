<?php

namespace App\Imports;

use App\Models\T_inventaris;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;

class InventarisImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $terminalId;
    private $imported = 0;
    private $skipped = 0;
    private $errors = [];

    public function __construct($terminalId)
    {
        $this->terminalId = $terminalId;
    }

    /**
     * Process the collection from Excel
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty rows
            if (empty($row['tipe']) && empty($row['serial_number'])) {
                $this->skipped++;
                continue;
            }

            try {
                T_inventaris::create([
                    'ID_TERMINAL' => $this->terminalId,
                    'ID_MERK' => $row['id_merk'] ?? null,
                    'TIPE' => $row['tipe'] ?? null,
                    'SERIAL_NUMBER' => $row['serial_number'] ?? null,
                    'TAHUN_PENGADAAN' => $row['tahun_pengadaan'] ?? null,
                    'KAPASITAS_PROSESSOR' => $row['kapasitas_prosessor'] ?? null,
                    'MEMORI_UTAMA' => $row['memori_utama'] ?? null,
                    'KAPASITAS_PENYIMPANAN' => $row['kapasitas_penyimpanan'] ?? null,
                    'SISTEM_OPERASI' => $row['sistem_operasi'] ?? null,
                    'USER_PENANGGUNG' => $row['user_penanggung'] ?? null,
                    'LOKASI_POSISI' => $row['lokasi_posisi'] ?? null,
                    'ID_KONDISI' => $row['id_kondisi'] ?? null,
                    'KETERANGAN' => $row['keterangan'] ?? null,
                    'ID_INSTAL' => $row['id_instal'] ?? null,
                    'ID_ANGGARAN' => $row['id_anggaran'] ?? null,
                    'KETERANGAN_ASSET' => $row['keterangan_asset'] ?? null,
                    'CREATE_BY' => auth()->user()->username ?? 'system'
                ]);

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $row->toArray(),
                    'error' => $e->getMessage()
                ];
                $this->skipped++;
            }
        }
    }

    /**
     * Get import results
     */
    public function getResults()
    {
        return [
            'imported' => $this->imported,
            'skipped'  => $this->skipped,
            'errors'   => $this->errors
        ];
    }
}
