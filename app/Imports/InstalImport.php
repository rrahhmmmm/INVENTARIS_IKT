<?php

namespace App\Imports;

use App\Models\M_instal;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;

class InstalImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $duplicates = [];
    private $imported = 0;
    private $skipped = 0;

    /**
     * Helper function untuk normalize nama instal
     * Hapus spasi dan convert ke uppercase
     */
    private function normalizeNamaInstal($nama)
    {
        return strtoupper(str_replace(' ', '', trim($nama)));
    }

    /**
     * Cek apakah nama instal sudah ada di database (normalized)
     */
    private function isDuplicateInDatabase($namaInstal)
    {
        $normalizedInput = $this->normalizeNamaInstal($namaInstal);

        return M_instal::whereRaw("UPPER(REPLACE(NAMA_INSTAL, ' ', '')) = ?", [$normalizedInput])
            ->exists();
    }

    /**
     * Process the collection from Excel
     */
    public function collection(Collection $rows)
    {
        // Array untuk track nama yang sudah diproses dalam file ini
        $processedNames = [];

        foreach ($rows as $row) {
            $namaInstal = trim($row['nama_instal'] ?? $row['NAMA_INSTAL'] ?? '');
            $createBy = trim($row['create_by'] ?? $row['CREATE_BY'] ?? '');

            // Skip jika nama kosong
            if (empty($namaInstal)) {
                $this->skipped++;
                continue;
            }

            $normalizedName = $this->normalizeNamaInstal($namaInstal);

            // Cek duplikat dalam file Excel yang sama
            if (in_array($normalizedName, $processedNames)) {
                $this->duplicates[] = [
                    'nama' => $namaInstal,
                    'reason' => 'Duplikat dalam file Excel'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat di database
            if ($this->isDuplicateInDatabase($namaInstal)) {
                $this->duplicates[] = [
                    'nama' => $namaInstal,
                    'reason' => 'Sudah ada di database'
                ];
                $this->skipped++;
                continue;
            }

            // Tambah ke processed names
            $processedNames[] = $normalizedName;

            // Insert ke database
            M_instal::create([
                'NAMA_INSTAL' => $namaInstal,
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
            'imported' => $this->imported,
            'skipped'  => $this->skipped,
            'duplicates' => $this->duplicates
        ];
    }
}
