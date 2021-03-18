<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPageController extends Controller
{
    public function show(Request $request, $userId){
        $month = $request->month;
        $year = $request->year;

        $hours = DB::select('CALL get_user_hours(?,?,?)', [$userId, $month, $year]);

        $hours = $hours ? $hours[0] : [];

        return response()->json($hours);

        /*
        $date = '2020-10-28';

        while(strtotime($date) < strtotime('2022-01-01')){
            DB::table('production_calendar')->insert(['date' => $date]);
            $date = date("Y-m-d", strtotime($date . '+ 1 days'));
        }*/
    }
}
