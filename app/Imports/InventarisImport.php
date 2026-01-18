<?php

namespace App\Imports;

use App\Models\T_inventaris;
use App\Models\M_perangkat;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;

class InventarisImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $terminalId;
    private $perangkatId;
    private $perangkat;
    private $imported = 0;
    private $skipped = 0;
    private $errors = [];

    public function __construct($terminalId, $perangkatId = 1)
    {
        $this->terminalId = $terminalId;
        $this->perangkatId = $perangkatId;
        $this->perangkat = M_perangkat::find($perangkatId);
    }

    /**
     * Process the collection from Excel
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty rows - check mandatory fields
            if (empty($row['tipe']) && empty($row['lokasi_posisi'])) {
                $this->skipped++;
                continue;
            }

            try {
                // Create inventory record with mandatory fields
                $data = [
                    'ID_TERMINAL' => $this->terminalId,
                    'ID_PERANGKAT' => $this->perangkatId,
                    'ID_MERK' => $row['id_merk'] ?? null,
                    'TIPE' => $row['tipe'] ?? null,
                    'LOKASI_POSISI' => $row['lokasi_posisi'] ?? null,
                    'TAHUN_PENGADAAN' => $row['tahun_pengadaan'] ?? null,
                    'ID_KONDISI' => $row['id_kondisi'] ?? null,
                    'ID_ANGGARAN' => $row['id_anggaran'] ?? null,
                    'CREATE_BY' => auth()->user()->username ?? 'system'
                ];

                // Map param columns from Excel
                // Excel columns are named like "param1 (serial number)" or just "param1"
                for ($i = 1; $i <= 16; $i++) {
                    $paramKey = "param{$i}";

                    // Try different column naming patterns
                    foreach ($row->keys() as $colName) {
                        $colNameLower = strtolower($colName);
                        // Match "param1", "param1 (serial number)", etc.
                        if (preg_match("/^param{$i}(\s|$|\()/i", $colNameLower)) {
                            $data[$paramKey] = $row[$colName] ?? null;
                            break;
                        }
                    }
                }

                T_inventaris::create($data);
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
