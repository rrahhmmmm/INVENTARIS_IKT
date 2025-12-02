<?php

namespace App\Imports;

use App\Models\M_subdivisi;
use App\Models\M_divisi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class SubdivisiImport implements ToCollection, WithHeadingRow
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
     * Cek apakah NAMA_SUBDIVISI sudah ada di database (normalized)
     */
    private function isDuplicateInDatabase($namaSubdivisi)
    {
        $normalizedInput = $this->normalizeString($namaSubdivisi);

        return M_subdivisi::whereRaw("UPPER(REPLACE(NAMA_SUBDIVISI, ' ', '')) = ?", [$normalizedInput])
            ->exists();
    }

    /**
     * Cek apakah NAMA_DIVISI ada di database (normalized)
     * Return model divisi jika ditemukan
     */
    private function findDivisiByName($namaDivisi)
    {
        $normalizedInput = $this->normalizeString($namaDivisi);

        return M_divisi::whereRaw("UPPER(REPLACE(NAMA_DIVISI, ' ', '')) = ?", [$normalizedInput])
            ->first();
    }

    /**
     * Process the collection from Excel
     */
    public function collection(Collection $rows)
    {
        // Array untuk track NAMA_SUBDIVISI yang sudah diproses dalam file ini
        $processedNamaSubdivisi = [];

        foreach ($rows as $row) {
            $namaDivisi = trim($row['divisi'] ?? $row['DIVISI'] ?? '');
            $namaSubdivisi = trim($row['nama_subdivisi'] ?? $row['NAMA_SUBDIVISI'] ?? '');
            $kodeLokasi = trim($row['kode_lokasi'] ?? $row['KODE_LOKASI'] ?? '');
            $createBy = trim($row['create_by'] ?? $row['CREATE_BY'] ?? '');

            // Skip jika nama subdivisi kosong
            if (empty($namaSubdivisi)) {
                $this->skipped++;
                continue;
            }

            // Cari divisi berdasarkan nama (dengan normalisasi)
            $divisi = $this->findDivisiByName($namaDivisi);

            if (!$divisi) {
                $this->duplicates[] = [
                    'nama_subdivisi' => $namaSubdivisi,
                    'divisi'         => $namaDivisi,
                    'reason'         => 'Divisi tidak ditemukan di database'
                ];
                $this->skipped++;
                continue;
            }

            $normalizedNamaSubdivisi = $this->normalizeString($namaSubdivisi);

            // Cek duplikat dalam file Excel yang sama
            if (in_array($normalizedNamaSubdivisi, $processedNamaSubdivisi)) {
                $this->duplicates[] = [
                    'nama_subdivisi' => $namaSubdivisi,
                    'divisi'         => $namaDivisi,
                    'reason'         => 'Duplikat dalam file Excel'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat di database
            if ($this->isDuplicateInDatabase($namaSubdivisi)) {
                $this->duplicates[] = [
                    'nama_subdivisi' => $namaSubdivisi,
                    'divisi'         => $namaDivisi,
                    'reason'         => 'Sudah ada di database'
                ];
                $this->skipped++;
                continue;
            }

            // Tambah ke processed
            $processedNamaSubdivisi[] = $normalizedNamaSubdivisi;

            // Insert ke database
            M_subdivisi::create([
                'ID_DIVISI'      => $divisi->ID_DIVISI,
                'NAMA_SUBDIVISI' => $namaSubdivisi,
                'KODE_LOKASI'    => $kodeLokasi ?: null,
                'CREATE_BY'      => $createBy ?: (auth()->user()->username ?? 'system')
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