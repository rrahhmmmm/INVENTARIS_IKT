<?php

namespace App\Exports;

use App\Models\M_divisi;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;

class SubdivisiExportTemplate implements WithHeadings, WithEvents
{
    protected $divisi;

    public function __construct()
    {
        // ambil daftar divisi dari DB
        $this->divisi = M_divisi::pluck('NAMA_DIVISI')->toArray();
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'Divisi',
            'Nama Subdivisi',
            'Created By'
        ];
    }

    /**
     * Event untuk set dropdown
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Buat daftar divisi (misal di kolom Z untuk hidden)
                $row = 1;
                foreach ($this->divisi as $namaDivisi) {
                    $sheet->setCellValue("Z{$row}", $namaDivisi);
                    $row++;
                }

                // Definisikan named range (Z1:Z{jumlah_divisi})
                $highest = count($this->divisi);
                $sheet->getParent()->addNamedRange(
                    new \PhpOffice\PhpSpreadsheet\NamedRange(
                        'DivisiList',
                        $sheet,
                        "Z1:Z{$highest}"
                    )
                );

                // Terapkan data validation ke kolom A (Divisi)
                for ($i = 2; $i <= 100; $i++) { // batas 100 baris, bisa disesuaikan
                    $validation = $sheet->getCell("A{$i}")->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input salah');
                    $validation->setError('Pilih divisi dari daftar');
                    $validation->setPromptTitle('Pilih Divisi');
                    $validation->setPrompt('Silakan pilih divisi dari dropdown');
                    $validation->setFormula1('=DivisiList');
                }

                // Sembunyikan kolom Z (tempat daftar divisi)
                $sheet->getColumnDimension('Z')->setVisible(false);
            }
        ];
    }
}
