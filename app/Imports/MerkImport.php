<?php

namespace App\Imports;

use App\Models\M_merk;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;

class MerkImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $duplicates = [];
    private $imported = 0;
    private $skipped = 0;

    /**
     * Helper function untuk normalize nama merk
     * Hapus spasi dan convert ke uppercase
     */
    private function normalizeNamaMerk($nama)
    {
        return strtoupper(str_replace(' ', '', trim($nama)));
    }

    /**
     * Cek apakah nama merk sudah ada di database (normalized)
     */
    private function isDuplicateInDatabase($namaMerk)
    {
        $normalizedInput = $this->normalizeNamaMerk($namaMerk);

        return M_merk::whereRaw("UPPER(REPLACE(NAMA_MERK, ' ', '')) = ?", [$normalizedInput])
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
            $namaMerk = trim($row['nama_merk'] ?? $row['NAMA_MERK'] ?? '');
            $createBy = trim($row['create_by'] ?? $row['CREATE_BY'] ?? '');

            // Skip jika nama kosong
            if (empty($namaMerk)) {
                $this->skipped++;
                continue;
            }

            $normalizedName = $this->normalizeNamaMerk($namaMerk);

            // Cek duplikat dalam file Excel yang sama
            if (in_array($normalizedName, $processedNames)) {
                $this->duplicates[] = [
                    'nama' => $namaMerk,
                    'reason' => 'Duplikat dalam file Excel'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat di database
            if ($this->isDuplicateInDatabase($namaMerk)) {
                $this->duplicates[] = [
                    'nama' => $namaMerk,
                    'reason' => 'Sudah ada di database'
                ];
                $this->skipped++;
                continue;
            }

            // Tambah ke processed names
            $processedNames[] = $normalizedName;

            // Insert ke database
            M_merk::create([
                'NAMA_MERK' => $namaMerk,
                'CREATE_BY' => $createBy ?: (auth()->user()->username ?? 'system')
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
