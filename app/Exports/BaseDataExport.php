<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RowType;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BaseDataExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $exportable = null;
    protected $data = null;
    protected $title = "";
    protected $type = "xlsx";

    public function __construct(object $exportable, $data = [], $title = "", $type = "pdf")
    {
        $request = resolve('request');
        if (isset($exportable)) {
            $this->exportable = $exportable;
        }
        if (isset($data)) {
            $this->data = $data;
        }
        $this->title = $title;

        $this->type = isset($request->type_export) ? $request->type_export : $type;
        if ($this->type === "excel") {
            $this->type = "xlsx";
        }
        $this->fileName = "{$title}.{$this->type}";
        $this->writerType =  $this->type == "xlsx" ? Excel::XLSX : Excel::DOMPDF;
    }

    public function headings(): array
    {
        $headings = [];
        if ($this->exportable != null) {
            foreach ($this->exportable as $dataIndex => $title) {
                $headings[] = $title;
            }
        }
        return $headings;
    }

    public function collection()
    {
        return $this->data;
    }

    public function map($row): array
    {
        $row_col = [];
        foreach ($this->exportable as $dataIndex => $title) {
            $row_col[] = data_get($row, $dataIndex);
        }
        return $row_col;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }

}
