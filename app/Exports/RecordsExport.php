<?php

namespace App\Exports;

use App\Project;
use App\Record;
use App\User;
use App\Department;
use DateTime;
use http\Exception\BadMessageException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Http\Controllers\Scripts\RandomColor;

class RecordsExport implements ShouldAutoSize, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     *//*
    public function collection()
    {
        return Record::all();
    }*/

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function($event) {

                $months = ['ЯНВАРЬ', 'ФЕВРАЛЬ', 'МАРТ', 'АПРЕЛЬ', 'МАЙ', 'ИЮНЬ', 'ИЮЛЬ', 'АВГУСТ', 'СЕНТЯБРЬ', 'ОКТЯБРЬ', 'НОЯБРЬ', 'ДЕКАБРЬ'];

                $daysOfWeek = ['ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ', 'ВС'];

                $monthNumber = request()->get('month') ? request()->get('month') : date('m');

                $year = request()->get('year') ? request()->get('year') : date('Y');

                $sheet = $event->writer->getActiveSheet();
                $sheet->setTitle($year);
                $arr = [
                    ['Дата', 'ФИО', 'Код проекта', 'Вид работ', "Кол-во часов", "Название отдела", "Комментарий"],
                ];

                $date = $year . '-' . $monthNumber . '-01';

                $users = User::with('department')->get()->toArray();

                $amountStrings = 0;

                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNumber, $year);

                for ($j = 0; $j < $daysInMonth; $j++) {

                    $oldDate = $date;

                    foreach ($users as $user) {
                        $records = Record::with('user', 'project', 'department')
                            ->where([
                                ['date', $oldDate],
                                ['user_id', $user['id']],
                            ])->latest()->get()->toArray();
                        $new_date_format = date('d.m.Y', strtotime($oldDate));
                        $new_date_format = Date::PHPToExcel($new_date_format);
                        if (!empty($records)) {
                            foreach ($records as $record) {
                                $desc = preg_replace('/[\r\n]/', ' ', $record['description']);
                                $arr[] = [
                                    (string)$new_date_format,
                                    $record['user']['name'],
                                    $record['project']['title'],
                                    $desc,
                                    (string)$record['hours'],
                                    $user['department'] != null ? $user['department']['title'] : '',
                                    ''
                                ];
                            }
                        } else {
                            $arr[] = [
                                (string)$new_date_format,
                                $user['name'],
                                '',
                                '',
                                0,
                                $user['department'] != null ? $user['department']['title'] : '',
                                '',
                            ];
                        }
                        $amountStrings++;
                    }
                    $date = date("Y-m-d", strtotime($oldDate . '+ 1 days'));
                }

                //dd($arr);

                $sheet->fromArray($arr);

                for ($i = 1; $i < $amountStrings * 1.5; $i++) {
                    $sheet->getStyle("A$i")
                        ->getNumberFormat()
                        ->setFormatCode('dd.mm.yyyy');
                }
                /*
                                $sheet->getColumnDimension('B')->setAutoSize(true);
                                $sheet->getColumnDimension('C')->setAutoSize(true);
                                $sheet->getColumnDimension('F')->setAutoSize(true);
                                $sheet->getColumnDimension('E')->setAutoSize(true);
                */
                /*$sheet->mergeCells('B1:AF1');
                $sheet->mergeCells('B2:AF2');

                $arr = [];
                for ($i = 0; $i < $daysInMonth; $i++){ //calendar
                    if($firstDayOfWeek > 6)
                        $firstDayOfWeek = 0;
                    $arr[0][$i] = $i + 1;
                    $arr[1][$i] = $daysOfWeek[$firstDayOfWeek];
                    $firstDayOfWeek++;
                }
                $sheet->fromArray($arr, null, 'B3');
                $sheet->mergeCells('A3:A4');

                $sheet->setCellValue('A5', 'ФИО');

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THICK,
                        ],
                    ],
                ];

                $workers = User::select(DB::raw('id, name'))
                    ->orderBy('name')
                    ->get()
                    ->toArray();
                $countPrj = 0;
                $rowsAmount = 1;
                $c = 0;
                $tempPrj = Project::select('id', 'name', 'color')->get()->toArray();
                for ($i = 0; $i < count($workers); $i++) { //Сотрудники
                    $worker = $workers[$i];
                    $projects = Record::join('projects', 'projects.id', '=', 'records.project_id')
                        ->select('projects.id as id', 'projects.name as name', 'projects.color as color')
                        ->where('records.worker_id', $worker['id'])
                        ->whereMonth('date', $monthNumber)
                        ->whereYear('date', $year)
                        ->distinct()
                        ->get()
                        ->toArray();
                    $row = $i + 6;
                    $sheet->setCellValueByColumnAndRow(1, $row + $countPrj, $worker['name']);
                    $workerEndCoord = $sheet->getCellByColumnAndRow($countProjects + $daysInMonth + 2, $row + $countPrj)->getCoordinate();
                    $sheet->getStyle($workerEndCoord)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue($workerEndCoord, $worker['name']);
                    $sumWorkersHoursCellCoord = $sheet->getCellByColumnAndRow($countProjects + $daysInMonth + 3, $row + $countPrj)->getCoordinate();
                    $startNameCellCoord =  $sheet->getCellByColumnAndRow(1, $row + $countPrj)->getCoordinate();
                    $startHourCellCoord = $sheet->getCellByColumnAndRow($daysInMonth + 2, $row + $countPrj)->getCoordinate();
                    $count = count($projects);
                    if($count == 0){
                        $projects[] = [
                            'id' => 0
                        ];
                        $count++;
                    }
                    $countPrj += $count - 1;
                    for ($k = 0; $k < $count; $k++) { //Проекты
                        $project = $projects[$k];
                        $dateTimes = Record::select(DB::raw('DAY(date) as day, SUM(timeAmount) as timeAmount'))
                            ->where([
                                'worker_id' => $worker['id'],
                                'project_id' => $project['id'],
                            ])
                            ->whereMonth('date', $monthNumber)
                            ->whereYear('date', $year)
                            ->groupBy('day')
                            ->get()
                            ->toArray();
                        $rowsAmount++;
                        $index = $k;
                        if($i > 0){
                            $index = $k + $c;
                        }
                        for ($j = 0; $j < count($dateTimes); $j++) { //Часы
                            $sheet->setCellValueByColumnAndRow($dateTimes[$j]['day'] + 1, $row + $index, $dateTimes[$j]['timeAmount']);
                            $sheet->getCellByColumnAndRow($dateTimes[$j]['day'] + 1, $row + $index)
                                ->getStyle()
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB($project['color']);
                        }
                        $prjIndex = array_search($project, $tempPrj) + 1;
                        $sumCellCoord = $sheet->getCellByColumnAndRow($daysInMonth + $prjIndex - 1, $row + $index)->getCoordinate();
                        $startCellCoord = $sheet->getCellByColumnAndRow(2, $row + $index)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($daysInMonth + $prjIndex + 1, $row + $index, '=SUM('.$startCellCoord.':'.$sumCellCoord.')');
                    }
                    $endHourCellCoord = $sheet->getCellByColumnAndRow($daysInMonth + 1 + $countProjects, $row + $countPrj)->getCoordinate();
                    $sheet->setCellValue($sumWorkersHoursCellCoord, '=SUM('.$startHourCellCoord.':'.$endHourCellCoord.')');
                    $endNameCellCoord =  $sheet->getCellByColumnAndRow(1, $row + $countPrj)->getCoordinate();
                    //$endRowsCellCoord = $sheet->getCellByColumnAndRow($daysInMonth + 1 + $countProjects, $row + $countPrj)->getCoordinate();
                    //$sheet->getStyle($startNameCellCoord.':'.$endRowsCellCoord)->applyFromArray($styleArray);
                    $sheet->mergeCells($startNameCellCoord.':'.$endNameCellCoord);
                    $reasons = Record::join('reasons', 'reasons.id', '=', 'records.reason_id') //Причины
                        ->select(DB::raw('DAY(date) as day, reasons.name as name'))
                        ->where('worker_id', $worker['id'])
                        ->whereMonth('date', $monthNumber)
                        ->whereYear('date', $year)
                        ->groupBy(['day', 'name'])
                        ->get()
                        ->toArray();
                    $countReasons = count($reasons);
                    for ($n = 0; $n < $countReasons; $n++){
                        $sheet->setCellValueByColumnAndRow($reasons[$n]['day'] + 1, $row + $countPrj, $reasons[$n]['name']);
                        $sheet->getCellByColumnAndRow($reasons[$n]['day'] + 1, $row + $countPrj)
                            ->getStyle()
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFFFFF');
                    }
                    if($count > 1)
                        $c++;
                }
                $sheet->getRowDimension('18')->setRowHeight(30);
                $prjCount = count($tempPrj); //вывод часов проектов , суммы часов проектов, цветов проектов, данных проектов
                $countTopRows = 5;
                $styleArray = [
                    'font' => [
                        'name' => 'Times New Roman',
                        'size' => 14
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '',
                        ],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];
                $c = 0;
                for($i = 0; $i < $prjCount; $i++){
                    $index = $i + 2;
                    $col = $daysInMonth + $index;
                    $startCoord = $sheet->getCellByColumnAndRow($col, $countTopRows - 2 + 1)->getCoordinate();
                    $endCoord = $sheet->getCellByColumnAndRow($col, $rowsAmount - 1 + $countTopRows)->getCoordinate();
                    $sheet->setCellValueByColumnAndRow($col, $rowsAmount + $countTopRows, '=SUM('.$startCoord.':'.$endCoord.')');
                    $styleArray['fill']['startColor']['rgb'] = $tempPrj[$i]['color'];
                    $endCoordStyle = $sheet->getCellByColumnAndRow($col, $rowsAmount + $countTopRows)->getCoordinate();
                    $sheet->getStyle($startCoord.':'.$endCoordStyle)->applyFromArray($styleArray);

                    $index = $i + $c + 2;

                    $leftTopCellCoord = $sheet->getCellByColumnAndRow($index, $rowsAmount + $countTopRows + 3)->getCoordinate();
                    $leftBottomCellCoord = $sheet->getCellByColumnAndRow($index, $rowsAmount + $countTopRows + 4)->getCoordinate();
                    $leftTopCellCoordi = $sheet->getCellByColumnAndRow($index + 1, $rowsAmount + $countTopRows + 3)->getCoordinate();
                    $leftBottomCellCoordi = $sheet->getCellByColumnAndRow($index + 1, $rowsAmount + $countTopRows + 4)->getCoordinate();
                    $sumPrjHoursCellCoord = $sheet->getCellByColumnAndRow($i + $daysInMonth + 2, $rowsAmount + $countTopRows)->getCoordinate();
                    $sheet->setCellValue($leftTopCellCoord, $tempPrj[$i]['name']);
                    $sheet->setCellValue($leftBottomCellCoord, '='.$sumPrjHoursCellCoord);
                    $styleArray['fill']['startColor']['rgb'] = 'ffffff';
                    $sheet->getStyle($leftTopCellCoord.':'.$leftTopCellCoordi)->applyFromArray($styleArray);
                    $styleArray['fill']['startColor']['rgb'] = $tempPrj[$i]['color'];
                    $sheet->getStyle($leftBottomCellCoord.':'.$leftBottomCellCoordi)->applyFromArray($styleArray);
                    $sheet->mergeCells($leftTopCellCoord.':'.$leftTopCellCoordi);
                    $sheet->mergeCells($leftBottomCellCoord.':'.$leftBottomCellCoordi);
                    $c++;
                }

                $styleArray = [
                    'font' => [
                        'name' => 'Trebuchet MS',
                        'size' => 11
                    ],
                ];

                $sheet->getStyle('A1:AF5')->applyFromArray($styleArray);

                $styleArray = [
                    'font' => [
                        'name' => 'Bookman Old Style',
                        'size' => 14
                    ],
                ];

                $sheet->getStyle('B1:B2')->applyFromArray($styleArray);

                $sheet->getStyle('A1:AF'.(5 + count($workers) + $countPrj))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:AF'.(5 + count($workers) + $countPrj))->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);

                $styleArray = [
                    'font' => [
                        'name' => 'TimesNewRoman',
                        'size' => 14
                    ],
                ];

                $sheet->getStyle('B6:AF'.(5 + count($workers) + $countPrj))->applyFromArray($styleArray);

                $styleArray = [
                    'font' => [
                        'name' => 'Trebuchet MS',
                        'size' => 14
                    ],
                ];

                $sheet->getStyle('A6:A'.(5 + count($workers) + $countPrj))->applyFromArray($styleArray);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ];

                $sheet->getStyle('B3:'.($daysInMonth > 30 ? 'AF' : 'AE').(5 + count($workers) + $countPrj))->applyFromArray($styleArray);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                        'right' => [
                            'borderStyle' => Border::BORDER_THICK,
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THICK,
                        ],
                    ],
                ];

                $sheet->getStyle('A1:A'.(5 + count($workers)  + $countPrj))->applyFromArray($styleArray);

                $styleArray = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THICK,
                        ],
                    ],
                ];

                $sheet->getStyle('A1:A5')->applyFromArray($styleArray);

                $styleArray = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                        ],
                    ],
                ];

                $sheet->getStyle('A1:'.($daysInMonth > 30 ? 'AF' : 'AE').(5 + count($workers) + $countPrj))->applyFromArray($styleArray);

                $styleArray = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THICK,
                        ],
                    ],
                ];

                $sheet->getStyle('B2:'.($daysInMonth > 30 ? 'AF2' : 'AE2'))->applyFromArray($styleArray);

                $sheet->getSheetView()->setZoomScale(80);*/
            }
        ];
    }
}
