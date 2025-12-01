<?php

namespace App\Imports;

use App\Models\M_divisi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;

class DivisiImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $duplicates = [];
    private $imported = 0;
    private $skipped = 0;

    /**
     * Helper function untuk normalize nama divisi
     * Hapus spasi dan convert ke uppercase
     */
    private function normalizeNamaDivisi($nama)
    {
        return strtoupper(str_replace(' ', '', trim($nama)));
    }

    /**
     * Cek apakah nama divisi sudah ada di database (normalized)
     */
    private function isDuplicateInDatabase($namaDivisi)
    {
        $normalizedInput = $this->normalizeNamaDivisi($namaDivisi);

        return M_divisi::whereRaw("UPPER(REPLACE(NAMA_DIVISI, ' ', '')) = ?", [$normalizedInput])
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
            $namaDivisi = trim($row['nama_divisi'] ?? $row['NAMA_DIVISI'] ?? '');
            $createBy = trim($row['create_by'] ?? $row['CREATE_BY'] ?? '');

            // Skip jika nama kosong
            if (empty($namaDivisi)) {
                $this->skipped++;
                continue;
            }

            $normalizedName = $this->normalizeNamaDivisi($namaDivisi);

            // Cek duplikat dalam file Excel yang sama
            if (in_array($normalizedName, $processedNames)) {
                $this->duplicates[] = [
                    'nama' => $namaDivisi,
                    'reason' => 'Duplikat dalam file Excel'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat di database
            if ($this->isDuplicateInDatabase($namaDivisi)) {
                $this->duplicates[] = [
                    'nama' => $namaDivisi,
                    'reason' => 'Sudah ada di database'
                ];
                $this->skipped++;
                continue;
            }

            // Tambah ke processed names
            $processedNames[] = $normalizedName;

            // Insert ke database
            M_divisi::create([
                'NAMA_DIVISI' => $namaDivisi,
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