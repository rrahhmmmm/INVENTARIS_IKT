<?php

namespace App\Imports;

use App\Models\M_klasifikasi;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class KlasifikasiImport implements ToCollection, WithHeadingRow
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
     * Cek apakah KODE_KLASIFIKASI sudah ada di database (normalized)
     */
    private function isDuplicateKodeInDatabase($kode)
    {
        $normalizedInput = $this->normalizeString($kode);

        return M_klasifikasi::whereRaw("UPPER(REPLACE(KODE_KLASIFIKASI, ' ', '')) = ?", [$normalizedInput])
            ->exists();
    }

    /**
     * Cek apakah KATEGORI sudah ada di database (normalized)
     */
    private function isDuplicateKategoriInDatabase($kategori)
    {
        $normalizedInput = $this->normalizeString($kategori);

        return M_klasifikasi::whereRaw("UPPER(REPLACE(KATEGORI, ' ', '')) = ?", [$normalizedInput])
            ->exists();
    }

    /**
     * Process the collection from Excel
     */
    public function collection(Collection $rows)
    {
        // Array untuk track yang sudah diproses dalam file ini
        $processedKodes = [];
        $processedKategoris = [];

        foreach ($rows as $row) {
            $kodeKlasifikasi = trim($row['kode_klasifikasi'] ?? $row['KODE_KLASIFIKASI'] ?? '');
            $kategori = trim($row['kategori'] ?? $row['KATEGORI'] ?? '');
            $deskripsi = trim($row['deskripsi'] ?? $row['DESKRIPSI'] ?? '');
            $startDate = $row['start_date'] ?? $row['START_DATE'] ?? null;
            $endDate = $row['end_date'] ?? $row['END_DATE'] ?? null;
            $createBy = trim($row['create_by'] ?? $row['CREATE_BY'] ?? '');

            // Skip jika kode atau kategori kosong
            if (empty($kodeKlasifikasi) || empty($kategori)) {
                $this->skipped++;
                continue;
            }

            $normalizedKode = $this->normalizeString($kodeKlasifikasi);
            $normalizedKategori = $this->normalizeString($kategori);

            // Cek duplikat KODE dalam file Excel yang sama
            if (in_array($normalizedKode, $processedKodes)) {
                $this->duplicates[] = [
                    'kode' => $kodeKlasifikasi,
                    'kategori' => $kategori,
                    'reason' => 'Kode duplikat dalam file Excel'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat KATEGORI dalam file Excel yang sama
            if (in_array($normalizedKategori, $processedKategoris)) {
                $this->duplicates[] = [
                    'kode' => $kodeKlasifikasi,
                    'kategori' => $kategori,
                    'reason' => 'Kategori duplikat dalam file Excel'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat KODE di database
            if ($this->isDuplicateKodeInDatabase($kodeKlasifikasi)) {
                $this->duplicates[] = [
                    'kode' => $kodeKlasifikasi,
                    'kategori' => $kategori,
                    'reason' => 'Kode sudah ada di database'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat KATEGORI di database
            if ($this->isDuplicateKategoriInDatabase($kategori)) {
                $this->duplicates[] = [
                    'kode' => $kodeKlasifikasi,
                    'kategori' => $kategori,
                    'reason' => 'Kategori sudah ada di database'
                ];
                $this->skipped++;
                continue;
            }

            // Tambah ke processed
            $processedKodes[] = $normalizedKode;
            $processedKategoris[] = $normalizedKategori;

            // Insert ke database
            M_klasifikasi::create([
                'KODE_KLASIFIKASI' => $kodeKlasifikasi,
                'KATEGORI'         => $kategori,
                'DESKRIPSI'        => $deskripsi ?: null,
                'START_DATE'       => $startDate ?: null,
                'END_DATE'         => $endDate ?: null,
                'CREATE_BY'        => $createBy ?: (auth()->user()->username ?? 'system')
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