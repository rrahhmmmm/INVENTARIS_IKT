<?php

namespace App\Exports;

use App\Models\M_perangkat;
use App\Models\M_merk;
use App\Models\M_kondisi;
use App\Models\M_anggaran;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class InventarisExportTemplate implements WithHeadings, ShouldAutoSize, WithEvents
{
    protected $perangkatId;
    protected $perangkat;
    protected $merkList;
    protected $kondisiList;
    protected $anggaranList;

    public function __construct($perangkatId = 1)
    {
        $this->perangkatId = $perangkatId;
        $this->perangkat = M_perangkat::find($perangkatId);
        $this->merkList = M_merk::pluck('NAMA_MERK')->toArray();
        $this->kondisiList = M_kondisi::pluck('NAMA_KONDISI')->toArray();
        $this->anggaranList = M_anggaran::pluck('NAMA_ANGGARAN')->toArray();
    }

    public function headings(): array
    {
        // Mandatory fields - gunakan NAMA bukan ID untuk user friendly
        $headers = [
            'MERK',
            'TIPE',
            'LOKASI_POSISI',
            'TAHUN_PENGADAAN',
            'KONDISI',
            'ANGGARAN',
        ];

        // Add param columns with their labels for reference
        if ($this->perangkat) {
            for ($i = 1; $i <= 16; $i++) {
                $fieldName = $this->perangkat->{"param$i"};
                if (!empty($fieldName)) {
                    // Column name is param1, param2, etc. with label in parentheses
                    $headers[] = "param{$i} ({$fieldName})";
                }
            }
        }

        return $headers;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $workbook = $sheet->getParent();
                $sheetName = $sheet->getTitle();

                // Kolom untuk data dropdown (tersembunyi)
                // X = Merk, Y = Kondisi, Z = Anggaran

                // Tulis daftar Merk di kolom X
                $row = 1;
                foreach ($this->merkList as $item) {
                    $sheet->setCellValue("X{$row}", $item);
                    $row++;
                }

                // Tulis daftar Kondisi di kolom Y
                $row = 1;
                foreach ($this->kondisiList as $item) {
                    $sheet->setCellValue("Y{$row}", $item);
                    $row++;
                }

                // Tulis daftar Anggaran di kolom Z
                $row = 1;
                foreach ($this->anggaranList as $item) {
                    $sheet->setCellValue("Z{$row}", $item);
                    $row++;
                }

                // Buat Named Ranges dengan formula OFFSET dinamis
                $workbook->addNamedRange(new NamedRange(
                    'MerkList',
                    $sheet,
                    "OFFSET('{$sheetName}'!\$X\$1,0,0,COUNTA('{$sheetName}'!\$X:\$X),1)"
                ));
                $workbook->addNamedRange(new NamedRange(
                    'KondisiList',
                    $sheet,
                    "OFFSET('{$sheetName}'!\$Y\$1,0,0,COUNTA('{$sheetName}'!\$Y:\$Y),1)"
                ));
                $workbook->addNamedRange(new NamedRange(
                    'AnggaranList',
                    $sheet,
                    "OFFSET('{$sheetName}'!\$Z\$1,0,0,COUNTA('{$sheetName}'!\$Z:\$Z),1)"
                ));

                // Terapkan dropdown pada baris 2-100
                for ($i = 2; $i <= 100; $i++) {
                    // Kolom A = MERK
                    $this->applyDropdown($sheet, "A{$i}", 'MerkList', 'Pilih Merk');

                    // Kolom E = KONDISI
                    $this->applyDropdown($sheet, "E{$i}", 'KondisiList', 'Pilih Kondisi');

                    // Kolom F = ANGGARAN
                    $this->applyDropdown($sheet, "F{$i}", 'AnggaranList', 'Pilih Anggaran');
                }

                // Sembunyikan kolom data
                $sheet->getColumnDimension('X')->setVisible(false);
                $sheet->getColumnDimension('Y')->setVisible(false);
                $sheet->getColumnDimension('Z')->setVisible(false);
            }
        ];
    }

    private function applyDropdown($sheet, $cell, $namedRange, $prompt)
    {
        $validation = $sheet->getCell($cell)->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input salah');
        $validation->setError('Pilih dari daftar yang tersedia');
        $validation->setPromptTitle($prompt);
        $validation->setPrompt('Silakan pilih dari dropdown');
        $validation->setFormula1("={$namedRange}");
    }
}
