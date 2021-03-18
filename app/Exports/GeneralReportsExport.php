<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class GeneralReportsExport extends DefaultValueBinder implements ShouldAutoSize, FromArray, WithHeadings, WithMapping, WithColumnFormatting, WithCustomValueBinder, WithColumnWidths, WithStyles
{
    protected $records;
    private $row = 1;

    public function headings(): array
    {
        return [
            'Дата',
            'ФИО',
            'Код проекта',
            'Вид работ',
            'Кол-во часов',
            'Название отдела',
            'Комментарий',
            'Уникальные дни'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'D' => 55,
            'G' => 55,
            'H' => 20
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getRowDimension('1')->setRowHeight(30);

        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [
            ['font' => [
                'name' => 'Calibri',
                'size' => 11
            ]],
            1 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }

    public function map($records): array
    {
        return [
            Date::PHPToExcel($records['date']),
            $records['name'],
            $records['project'],
            $records['description'],
            $records['hours'],
            $records['department'],
            '',
            DataType::TYPE_NULL
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() == 'H' && $cell->getRow() > 1) {
            $r1 = $this->row;
            $r2 = $this->row + 1;
            $formula = '=IF(OR(E'.$r2.'=" ", E'.$r2.'=0, E'.$r2.'="К"), 0, IF(CONCATENATE(A'.$r2.', B'.$r2.')=CONCATENATE(A'.$r1.', B'.$r1.'), 0, 1))';
            $cell->setValueExplicit($formula, DataType::TYPE_FORMULA);

            $this->row++;

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function array(): array
    {
        return $this->records;
    }
}
