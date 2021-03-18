<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ReportsExport implements ShouldAutoSize, FromArray, WithHeadings
{
    protected $records;

    public function headings(): array
    {
        return [
            'Дата',
            'Проект',
            'Сотрудник',
            'Часы',
            'Описание'
        ];
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
