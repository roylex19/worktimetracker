<?php

namespace App\Http\Controllers;

use App\Exports\GeneralReportsExport;
use App\Exports\RecordsExport;
use App\Exports\ReportsExport;
use App\Filters\RecordsFilter;
use App\Record;
use App\Sorting\RecordsSort;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RecordsController extends Controller
{
    public function download(){
        return Excel::download(new RecordsExport, 'Отчет о проделанной работе '. date('d.m.Y') .'.xlsx');
    }
}
