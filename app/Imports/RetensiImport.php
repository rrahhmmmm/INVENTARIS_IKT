<?php

namespace App\Imports;

use App\Models\M_retensi;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class RetensiImport implements ToCollection, WithHeadingRow
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
     * Generate unique key dari kombinasi 4 field
     */
    private function generateUniqueKey($jenisArsip, $bidangArsip, $tipeArsip, $detailTipeArsip)
    {
        return $this->normalizeString($jenisArsip) . '|' .
               $this->normalizeString($bidangArsip) . '|' .
               $this->normalizeString($tipeArsip) . '|' .
               $this->normalizeString($detailTipeArsip);
    }

    /**
     * Cek apakah kombinasi sudah ada di database (normalized)
     */
    private function isDuplicateInDatabase($jenisArsip, $bidangArsip, $tipeArsip, $detailTipeArsip)
    {
        $normalizedJenis = $this->normalizeString($jenisArsip);
        $normalizedBidang = $this->normalizeString($bidangArsip);
        $normalizedTipe = $this->normalizeString($tipeArsip);
        $normalizedDetail = $this->normalizeString($detailTipeArsip);

        return M_retensi::whereRaw("UPPER(REPLACE(JENIS_ARSIP, ' ', '')) = ?", [$normalizedJenis])
            ->whereRaw("UPPER(REPLACE(BIDANG_ARSIP, ' ', '')) = ?", [$normalizedBidang])
            ->whereRaw("UPPER(REPLACE(TIPE_ARSIP, ' ', '')) = ?", [$normalizedTipe])
            ->whereRaw("UPPER(REPLACE(DETAIL_TIPE_ARSIP, ' ', '')) = ?", [$normalizedDetail])
            ->exists();
    }

    /**
     * Process the collection from Excel
     */
    public function collection(Collection $rows)
    {
        // Array untuk track kombinasi yang sudah diproses dalam file ini
        $processedKeys = [];

        foreach ($rows as $row) {
            $jenisArsip = trim($row['jenis_arsip'] ?? $row['JENIS_ARSIP'] ?? '');
            $bidangArsip = trim($row['bidang_arsip'] ?? $row['BIDANG_ARSIP'] ?? '');
            $tipeArsip = trim($row['tipe_arsip'] ?? $row['TIPE_ARSIP'] ?? '');
            $detailTipeArsip = trim($row['detail_tipe_arsip'] ?? $row['DETAIL_TIPE_ARSIP'] ?? '');
            $masaAktif = $row['masa_aktif'] ?? $row['MASA_AKTIF'] ?? null;
            $descAktif = trim($row['desc_aktif'] ?? $row['DESC_AKTIF'] ?? '');
            $masaInaktif = $row['masa_inaktif'] ?? $row['MASA_INAKTIF'] ?? null;
            $descInaktif = trim($row['desc_inaktif'] ?? $row['DESC_INAKTIF'] ?? '');
            $keterangan = trim($row['keterangan'] ?? $row['KETERANGAN'] ?? '');
            $createBy = trim($row['create_by'] ?? $row['CREATE_BY'] ?? '');

            // Skip jika field wajib kosong
            if (empty($jenisArsip) || empty($bidangArsip) || empty($tipeArsip)) {
                $this->skipped++;
                continue;
            }

            $uniqueKey = $this->generateUniqueKey($jenisArsip, $bidangArsip, $tipeArsip, $detailTipeArsip);

            // Cek duplikat dalam file Excel yang sama
            if (in_array($uniqueKey, $processedKeys)) {
                $this->duplicates[] = [
                    'jenis_arsip'       => $jenisArsip,
                    'bidang_arsip'      => $bidangArsip,
                    'tipe_arsip'        => $tipeArsip,
                    'detail_tipe_arsip' => $detailTipeArsip,
                    'reason'            => 'Duplikat dalam file Excel'
                ];
                $this->skipped++;
                continue;
            }

            // Cek duplikat di database
            if ($this->isDuplicateInDatabase($jenisArsip, $bidangArsip, $tipeArsip, $detailTipeArsip)) {
                $this->duplicates[] = [
                    'jenis_arsip'       => $jenisArsip,
                    'bidang_arsip'      => $bidangArsip,
                    'tipe_arsip'        => $tipeArsip,
                    'detail_tipe_arsip' => $detailTipeArsip,
                    'reason'            => 'Sudah ada di database'
                ];
                $this->skipped++;
                continue;
            }

            // Tambah ke processed keys
            $processedKeys[] = $uniqueKey;

            // Insert ke database
            M_retensi::create([
                'JENIS_ARSIP'       => $jenisArsip,
                'BIDANG_ARSIP'      => $bidangArsip,
                'TIPE_ARSIP'        => $tipeArsip,
                'DETAIL_TIPE_ARSIP' => $detailTipeArsip ?: null,
                'MASA_AKTIF'        => $masaAktif ?: null,
                'DESC_AKTIF'        => $descAktif ?: null,
                'MASA_INAKTIF'      => $masaInaktif ?: null,
                'DESC_INAKTIF'      => $descInaktif ?: null,
                'KETERANGAN'        => $keterangan ?: null,
                'CREATE_BY'         => $createBy ?: (auth()->user()->username ?? 'system')
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