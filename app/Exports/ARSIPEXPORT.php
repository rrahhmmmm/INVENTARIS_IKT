<?php

namespace App\Exports;

use App\Models\T_arsip;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ARSIPEXPORT implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $divisiId;
    protected $no = 0;

    public function __construct($divisiId = null)
    {
        $this->divisiId = $divisiId;
    }

    /**
     * Ambil data arsip dengan relasi divisi dan subdivisi
     */
    public function collection()
    {
        $query = T_arsip::with(['divisi', 'subdivisi']);

        // Filter berdasarkan divisi jika bukan admin
        if ($this->divisiId) {
            $query->where('ID_DIVISI', $this->divisiId);
        }

        return $query->orderBy('ID_ARSIP', 'desc')->get();
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Divisi',
            'Subdivisi',
            'No Indeks',
            'No Berkas',
            'Judul Berkas',
            'No Isi Berkas',
            'Jenis Naskah Dinas',
            'Kode Klasifikasi',
            'No Nota Dinas',
            'Tanggal Berkas',
            'Perihal',
            'Tingkat Pengembangan',
            'Kondisi',
            'Lokasi Simpan',
            'Keterangan Simpan',
            'Tipe Retensi',
            'Tanggal Retensi',
            'Status',
            'Dibuat Oleh',
            'Diupdate Oleh',
            'Keterangan Update',
            'Link File'
        ];
    }

    /**
     * Mapping setiap row
     */
    public function map($arsip): array
    {
        $this->no++;

        // Generate link file jika ada
        $fileLink = '';
        if ($arsip->FILE) {
            $fileLink = url($arsip->FILE);
        }

        return [
            $this->no,
            $arsip->divisi->NAMA_DIVISI ?? '-',
            $arsip->subdivisi->NAMA_SUBDIVISI ?? '-',
            $arsip->NO_INDEKS ?? '-',
            $arsip->NO_BERKAS ?? '-',
            $arsip->JUDUL_BERKAS ?? '-',
            $arsip->NO_ISI_BERKAS ?? '-',
            $arsip->JENIS_ARSIP ?? '-',
            $arsip->KODE_KLASIFIKASI ?? '-',
            $arsip->NO_NOTA_DINAS ?? '-',
            $arsip->TANGGAL_BERKAS ?? '-',
            $arsip->PERIHAL ?? '-',
            $arsip->TINGKAT_PENGEMBANGAN ?? '-',
            $arsip->KONDISI ?? '-',
            $arsip->RAK_BAK_URUTAN ?? '-',
            $arsip->KETERANGAN_SIMPAN ?? '-',
            $arsip->TIPE_RETENSI ?? '-',
            $arsip->TANGGAL_RETENSI ?? '-',
            $arsip->KETERANGAN ?? '-',
            $arsip->CREATE_BY ?? '-',
            $arsip->UPDATE_BY ?? '-',
            $arsip->KETERANGAN_UPDATE ?? '-',
            $fileLink
        ];
    }

    /**
     * Styling untuk worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]
        ];
    }
}
