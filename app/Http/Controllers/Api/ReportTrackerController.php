<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Record;

class ReportTrackerController extends Controller
{
    public function show(){
        $days = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

        $records = [];

        for ($i = 0; $i < count($days); $i++){
            $record = Record::selectRaw('SUM(hours) as hours')
                ->where('user_id', auth()->user()->id)
                ->whereRaw("WEEKDAY(date) = $i")
                ->whereRaw("WEEK(date, 1) = WEEK(CURRENT_DATE(), 1)")
                ->get()[0];

            $hours = isset($record->hours) ? $record->hours : 0;
            //$date = isset($record->date) ? $record->date : null;

            $records[$i]['day'] = $days[$i];
            $records[$i]['hours'] = $hours;
            //$records[$i]['date'] = $date;
        }

        return response()->json($records);
    }
}
