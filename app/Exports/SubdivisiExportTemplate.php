<?php

namespace App\Exports;

use App\Models\M_divisi;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;

class SubdivisiExportTemplate implements WithHeadings, WithEvents
{
    protected $divisi;

    public function __construct()
    {
        $this->divisi = M_divisi::pluck('NAMA_DIVISI')->toArray();
    }

    public function headings(): array
    {
        return [
            'Divisi',
            'Nama Subdivisi',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $workbook = $sheet->getParent();
                $sheetName = $sheet->getTitle();

                // tulis daftar divisi di kolom Z
                $row = 1;
                foreach ($this->divisi as $namaDivisi) {
                    $sheet->setCellValue("Z{$row}", $namaDivisi);
                    $row++;
                }

                // pastikan sheet aktif
                $sheet->setSelectedCell('A1');

                // buat formula OFFSET (tanpa tanda =)
                $formula = "OFFSET('{$sheetName}'!\$Z\$1,0,0,COUNTA('{$sheetName}'!\$Z:\$Z),1)";

                // definisikan named range dinamis (kompatibel semua versi)
                $workbook->addNamedRange(
                    new NamedRange(
                        'DivisiList',
                        $sheet,
                        $formula
                    )
                );

                // dropdown validasi untuk kolom A
                for ($i = 2; $i <= 100; $i++) {
                    $validation = $sheet->getCell("A{$i}")->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(false);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input salah');
                    $validation->setError('Pilih divisi dari daftar');
                    $validation->setPromptTitle('Pilih Divisi');
                    $validation->setPrompt('Silakan pilih divisi dari dropdown');
                    $validation->setFormula1('=DivisiList');
                }

                $sheet->getColumnDimension('Z')->setVisible(false);
            }
        ];
    }
}
