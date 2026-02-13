<?php

namespace App\Imports;

use App\Models\T_inventaris;
use App\Models\M_perangkat;
use App\Models\M_merk;
use App\Models\M_kondisi;
use App\Models\M_anggaran;
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
            // Debug: Log column names to see how they're converted
            \Log::info('Import row keys: ' . json_encode($row->keys()->toArray()));
            \Log::info('Import row data: ' . json_encode($row->toArray()));

            // Skip empty rows - check mandatory fields
            if (empty($row['tipe']) && empty($row['lokasi_posisi'])) {
                $this->skipped++;
                continue;
            }

            try {
                // Lookup MERK: coba dengan nama dulu, fallback ke ID
                $merkId = null;
                $merkValue = $row['merk'] ?? $row['id_merk'] ?? null;
                if (!empty($merkValue)) {
                    if (is_numeric($merkValue)) {
                        $merkId = $merkValue;
                    } else {
                        $merkId = M_merk::where('NAMA_MERK', $merkValue)->value('ID_MERK');
                    }
                }

                // Lookup KONDISI: coba dengan nama dulu, fallback ke ID
                $kondisiId = null;
                $kondisiValue = $row['kondisi'] ?? $row['id_kondisi'] ?? null;
                if (!empty($kondisiValue)) {
                    if (is_numeric($kondisiValue)) {
                        $kondisiId = $kondisiValue;
                    } else {
                        $kondisiId = M_kondisi::where('NAMA_KONDISI', $kondisiValue)->value('ID_KONDISI');
                    }
                }

                // Lookup ANGGARAN: coba dengan nama dulu, fallback ke ID
                $anggaranId = null;
                $anggaranValue = $row['anggaran'] ?? $row['id_anggaran'] ?? null;
                if (!empty($anggaranValue)) {
                    if (is_numeric($anggaranValue)) {
                        $anggaranId = $anggaranValue;
                    } else {
                        $anggaranId = M_anggaran::where('NAMA_ANGGARAN', $anggaranValue)->value('ID_ANGGARAN');
                    }
                }

                // Create inventory record with mandatory fields
                $data = [
                    'ID_TERMINAL' => $this->terminalId,
                    'ID_PERANGKAT' => $this->perangkatId,
                    'ID_MERK' => $merkId,
                    'TIPE' => $row['tipe'] ?? null,
                    'LOKASI_POSISI' => $row['lokasi_posisi'] ?? null,
                    'TAHUN_PENGADAAN' => $row['tahun_pengadaan'] ?? null,
                    'ID_KONDISI' => $kondisiId,
                    'ID_ANGGARAN' => $anggaranId,
                    'CREATE_BY' => auth()->user()->username ?? 'system'
                ];

                // Map param columns from Excel
                // WithHeadingRow converts headers - need to match various patterns
                for ($i = 1; $i <= 16; $i++) {
                    $paramKey = "param{$i}";
                    $value = null;

                    // Try direct access first (exact match after conversion)
                    foreach ($row->keys() as $colName) {
                        $colNameStr = strtolower(trim((string)$colName));

                        // Check if column starts with "param{i}" followed by nothing, space, underscore, or parenthesis
                        if ($colNameStr === "param{$i}" ||
                            strpos($colNameStr, "param{$i}_") === 0 ||
                            strpos($colNameStr, "param{$i} ") === 0 ||
                            strpos($colNameStr, "param{$i}(") === 0) {
                            $value = $row[$colName];
                            break;
                        }
                    }

                    if ($value !== null && $value !== '') {
                        $data[$paramKey] = $value;
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
