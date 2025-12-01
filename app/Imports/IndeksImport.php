<?php

namespace App\Imports;

use App\Models\M_indeks;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class IndeksImport implements ToCollection, WithHeadingRow
{
    private $duplicates = [];
    private $imported = 0;
    private $skipped = 0;

    /**
     * Helper function untuk normalize string
     * Hapus spasi dan convert ke uppercase
     */
    private function normalizeString($value)
    {
        return strtoupper(str_replace(' ', '', trim($value ?? '')));
    }

    /**
     * Cek apakah NO_INDEKS sudah ada di database (normalized)
     */
    private function isDuplicateInDatabase($noIndeks)
    {
        $normalizedInput = $this->normalizeString($noIndeks);

        return M_indeks::whereRaw("UPPER(REPLACE(NO_INDEKS, ' ', '')) = ?", [$normalizedInput])
            ->exists();
    }

    /**
     * Process the collection from Excel
     */
    public function collection(Collection $rows)
    {
        // Array untuk track NO_INDEKS yang sudah diproses dalam file ini
        $processedNoIndeks = [];

        foreach ($rows as $row) {
            $noIndeks = trim($row['no_indeks'] ?? $row['NO_INDEKS'] ?? '');
            $wilayah = trim($row['wilayah'] ?? $row['WILAYAH'] ?? '');
            $namaIndeks = trim($row['nama_indeks'] ?? $row['NAMA_INDEKS'] ?? '');
            $startDate = $row['start_date'] ?? $row['START_DATE'] ?? null;
            $endDate = $row['end_date'] ?? $row['END_DATE'] ?? null;
            $createBy = trim($row['create_by'] ?? $row['CREATE_BY'] ?? '');

            // Skip jika NO_INDEKS kosong
            if (empty($noIndeks)) {
                $this->skipped++;
                continue;
            }

            $normalizedNoIndeks = $this->normalizeString($noIndeks);

            // Cek duplikat dalam file Excel yang sama
            if (in_array($normalizedNoIndeks, $processedNoIndeks)) {
                $this->duplicates[] = [
                    'no_indeks'   => $noIndeks,
                    'nama_indeks' => $namaIndeks,
                    'reason'      => 'Duplikat dalam file Excel'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat di database
            if ($this->isDuplicateInDatabase($noIndeks)) {
                $this->duplicates[] = [
                    'no_indeks'   => $noIndeks,
                    'nama_indeks' => $namaIndeks,
                    'reason'      => 'Sudah ada di database'
                ];
                $this->skipped++;
                continue;
            }

            // Tambah ke processed
            $processedNoIndeks[] = $normalizedNoIndeks;

            // Insert ke database
            M_indeks::create([
                'NO_INDEKS'   => $noIndeks,
                'WILAYAH'     => $wilayah ?: null,
                'NAMA_INDEKS' => $namaIndeks ?: null,
                'START_DATE'  => $startDate ?: null,
                'END_DATE'    => $endDate ?: null,
                'CREATE_BY'   => $createBy ?: (auth()->user()->username ?? 'system')
            ]);

            $this->imported++;
        }
    }

    /**
     * Get import results
     */
    public function getResults()
    {
        return [
            'imported'   => $this->imported,
            'skipped'    => $this->skipped,
            'duplicates' => $this->duplicates
        ];
    }
}