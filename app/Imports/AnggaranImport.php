<?php

namespace App\Imports;

use App\Models\M_anggaran;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;

class AnggaranImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $duplicates = [];
    private $imported = 0;
    private $skipped = 0;

    /**
     * Helper function untuk normalize nama anggaran
     * Hapus spasi dan convert ke uppercase
     */
    private function normalizeNamaAnggaran($nama)
    {
        return strtoupper(str_replace(' ', '', trim($nama)));
    }

    /**
     * Cek apakah nama anggaran sudah ada di database (normalized)
     */
    private function isDuplicateInDatabase($namaAnggaran)
    {
        $normalizedInput = $this->normalizeNamaAnggaran($namaAnggaran);

        return M_anggaran::whereRaw("UPPER(REPLACE(NAMA_ANGGARAN, ' ', '')) = ?", [$normalizedInput])
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
            $namaAnggaran = trim($row['nama_anggaran'] ?? $row['NAMA_ANGGARAN'] ?? '');
            $createBy = trim($row['create_by'] ?? $row['CREATE_BY'] ?? '');

            // Skip jika nama kosong
            if (empty($namaAnggaran)) {
                $this->skipped++;
                continue;
            }

            $normalizedName = $this->normalizeNamaAnggaran($namaAnggaran);

            // Cek duplikat dalam file Excel yang sama
            if (in_array($normalizedName, $processedNames)) {
                $this->duplicates[] = [
                    'nama' => $namaAnggaran,
                    'reason' => 'Duplikat dalam file Excel'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat di database
            if ($this->isDuplicateInDatabase($namaAnggaran)) {
                $this->duplicates[] = [
                    'nama' => $namaAnggaran,
                    'reason' => 'Sudah ada di database'
                ];
                $this->skipped++;
                continue;
            }

            // Tambah ke processed names
            $processedNames[] = $normalizedName;

            // Insert ke database
            M_anggaran::create([
                'NAMA_ANGGARAN' => $namaAnggaran,
                'CREATE_BY'     => $createBy ?: (auth()->user()->username ?? 'system')
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
