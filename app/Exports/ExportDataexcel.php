<?php


namespace App\Exports;


use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportDataexcel extends BaseDataExport
{
    private $first_cell_number = "A1";
    private $secon_cell_number = "A2";
    private $last_cell_number;

    public function __construct(object $exportable, $data = [], $title = "", $type = "pdf")
    {
        parent::__construct($exportable, $data, $title, $type);

    }


    private function setLastCellNumber(): string{

        $count_total_export = count($this->headings());
        $valeur = "";
        foreach (range('A', 'Z') as $index => $lettre) {
            if($index === $count_total_export -1){
                $valeur = $lettre;
                break;
            }
        }

        return $valeur;
    }

    public function styles(Worksheet $sheet)
    {

        $this->last_cell_number = $this->setLastCellNumber();

        $style_cell = $this->secon_cell_number.':'.$this->last_cell_number.(count($this->data) + 1);
        $sheet->getStyle($this->first_cell_number.':'.$this->last_cell_number.'1')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'font' => [
                'bold' => true,
                'size' => 13,
                'color' => [
                    'rgb' => 'fdfdfd',
                ],

            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'a17b4d',
                ],
            ],
        ]);


        $sheet->getStyle($style_cell)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'font' => [
                'bold' => false,
                'size' => 11,
            ],
        ]);
    }
}
