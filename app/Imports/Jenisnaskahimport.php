<?php

namespace App\Imports;

use App\Models\M_jenisnaskah;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class JenisNaskahImport implements ToCollection, WithHeadingRow
{
    private $duplicates = [];
    private $imported = 0;
    private $skipped = 0;

    private function normalizeString($value)
    {
        return strtoupper(str_replace(' ', '', trim($value ?? '')));
    }

    private function isDuplicateInDatabase($namaJenis)
    {
        $normalizedInput = $this->normalizeString($namaJenis);
        return M_jenisnaskah::whereRaw("UPPER(REPLACE(NAMA_JENIS, ' ', '')) = ?", [$normalizedInput])->exists();
    }

    public function collection(Collection $rows)
    {
        $processedNama = [];

        foreach ($rows as $row) {
            $namaJenis = trim($row['nama_jenis'] ?? $row['NAMA_JENIS'] ?? '');

            if (empty($namaJenis)) {
                $this->skipped++;
                continue;
            }

            $normalizedNama = $this->normalizeString($namaJenis);

            // Cek duplikat dalam file Excel
            if (in_array($normalizedNama, $processedNama)) {
                $this->duplicates[] = [
                    'nama_jenis' => $namaJenis,
                    'reason' => 'Duplikat dalam file Excel'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat di database
            if ($this->isDuplicateInDatabase($namaJenis)) {
                $this->duplicates[] = [
                    'nama_jenis' => $namaJenis,
                    'reason' => 'Sudah ada di database'
                ];
                $this->skipped++;
                continue;
            }

            $processedNama[] = $normalizedNama;

            M_jenisnaskah::create([
                'NAMA_JENIS' => $namaJenis,
                'CREATE_BY'  => auth()->user()->username ?? 'system'
            ]);

            $this->imported++;
        }
    }

    public function getResults()
    {
        return [
            'imported'   => $this->imported,
            'skipped'    => $this->skipped,
            'duplicates' => $this->duplicates
        ];
    }
}