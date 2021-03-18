<?php

namespace App\Http\Controllers\Api;

use App\Department_view;
use App\Exports\GeneralReportsExport;
use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use App\Record;
use App\User;
use App\Sorting\RecordsSort;
use App\Filters\RecordsFilter;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RecordsController extends Controller
{
    public function create()
    {
        $data = request()->post('data');

        $records = [];

        if(!$this->canReport($data[0]['date'])){
            return response()->json([
                'message' => 'Отчет возможно добавить только в течение последних 2 дней!',
                'type' => 'error',
            ]);
        }

        foreach ($data as $rec) {
            /*if($rec['hours'] == null){
                return response()->json([
                    'message' => 'Поле с часами не может быть пустым, введите часы',
                    'type' => 'error',
                ]);
            }
            if($rec['project'] == null){
                return response()->json([
                    'message' => 'Поле с проектом не может быть пустым, выберите проект',
                    'type' => 'error',
                ]);
            }*/

            $newRec = Record::create([
                'date' => $rec['date'],
                'user_id' => $rec['user']['id'],
                'description' => isset($rec['description']) ? $rec['description'] : '',
                'hours' => $rec['hours'],
                'project_id' => $rec['project']['id'],
                'boss_id' => $rec['boss_id']
            ]);
            $newRec->user = $rec['user'];
            $newRec->project = $rec['project'];
            $newRec->description = isset($rec['description']) ? $rec['description'] : '';
            $records[] = $newRec;
        }
        return response()->json([
            'message' => count($records) > 1 ? 'Записи успешно созданы' : 'Запись успешно создана',
            'type' => 'success',
            'records' => $records
        ]);
    }

    public function show(Request $request){
        $records = Record::query();

        $records = (new RecordsFilter($records, $request))->apply();

        $records = (new RecordsSort($records, $request))->apply();

        $records = $records->with(['user', 'project'])->paginate($this->countRecords);

        return response()->json($records);
    }

    public function showDepartment(Request $request, $id){
        $depsViews = Department_view::where('user_id', $id)->first();
        if($depsViews == null){
            $depsViews = [];
        }else{
            $depsViews = $depsViews->toArray()['departments'];
        }

        $depId = User::find($id)->department_id;
        array_push($depsViews, $depId);

        $records = Record::query();

        $records = (new RecordsFilter($records, $request))->apply();

        $records = (new RecordsSort($records, $request))->apply();

        $records = $records->with(['user', 'project'])->whereHas('user', function ($q) use ($depsViews){
            return $q->whereIn('department_id', $depsViews);
        })->orWhere('boss_id', $id)->paginate($this->countRecords);

        return response()->json($records);
    }

    public function showMy(Request $request, $id){
        $records = Record::with(['user', 'project'])
            ->where('user_id', $id)
            ->orWhere('boss_id', $id);

        $records = (new RecordsFilter($records, $request))->apply()->latest()->paginate($this->countRecords);

        return response()->json($records);
    }

    public function update($id){
        $data = request()->post('data');

        if(!$this->canReport($data['created_at'])){
            return response()->json([
                'message' => 'Отчет возможно изменить только в течение последних 2 дней!',
                'type' => 'error',
            ]);
        }

        $record = Record::find($id);
        $record->user_id = $data['user']['id'];
        $record->date = $data['date'];
        $record->project_id = $data['project']['id'];
        $record->hours = $data['hours'];
        $record->description = $data['description'];
        $record->save();
        $record->project = $data['project'];
        return response()->json([
            'message' => 'Запись успешно изменена',
            'type' => 'success',
            'record' => $record
        ]);
    }

    public function delete($id){
        $record = Record::find($id);

        if(!$this->canReport($record->created_at)){
            return response()->json([
                'message' => 'Отчет возможно удалить только в течение последних 2 дней!',
                'type' => 'error',
            ]);
        }

        $record->delete();

        return response()->json([
            'message' => 'Запись успешно удалена',
            'type' => 'success',
        ]);
    }
/*
    public function export(){
        $records = User::join('records', 'users.id', '=', 'records.user_id')
            ->join('project', 'project.id', '=', 'records.project_id')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->select('*')->get();

        return response()->json($records);
    }*/

    private function canReport($date){
        if(!auth()->user()->admin){
            $dayOfWeek = date('w');
            $origRecTime = strtotime($date);
            $recTime = strtotime(date('Y-m-d', $origRecTime));
            $time = strtotime(date('Y-m-d'));
            $dif2days = $time - strtotime('2 days');/*
            if($dayOfWeek == 1) {
                $dif2days = $time - strtotime('3 days');
            }
            else{
                $dif2days = $time - strtotime('2 days');
            }*/
            $dif = $recTime - $time;

            return !($dif < $dif2days || $dif > 0);
        }

        return true;
    }

    public function export(Request $request){
        $records = Record::query();

        $records = (new RecordsFilter($records, $request))->apply();

        $records = (new RecordsSort($records, $request))->apply();

        $records = $records
            ->join('projects', 'projects.id', '=', 'records.project_id')
            ->join('users', 'users.id', '=', 'records.user_id')
            ->select('date', 'projects.title', 'users.name', 'hours', 'description')
            ->get()
            ->toArray();

        return Excel::download(new ReportsExport($records), 'Отчет.xlsx');
    }

    public function exportGeneral(Request $request){
        //$records = (new RecordsFilter($records, $request))->apply();

        //$records = (new RecordsSort($records, $request))->apply();
/*
        $records = $records
            ->join('projects', 'projects.id', '=', 'records.project_id')
            ->join('users', 'users.id', '=', 'records.user_id')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->select('date', 'projects.title as project', 'users.name as name', 'hours', 'description', 'departments.title as department')
            ->orderBy('date')
            ->orderBy('name')
            ->orderBy('records.created_at')
            ->get()
            ->toArray();
*/
        $reports = [];

        $month = $request->month;
        $year = $request->year;

        $date = date("Y-m-d", mktime(0, 0, 0, $month, 1, $year));

        $users = User::with('department')->where('disabled', false)->get()->toArray();

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($j = 0; $j < $daysInMonth; $j++) {

            $oldDate = $date;

            foreach ($users as $user) {
                $records = Record::with('user', 'project', 'department')
                    ->where('date',  $oldDate)
                    ->where('user_id',  $user['id'])
                    ->latest()->get()->toArray();

                $newDate = date('d.m.Y', strtotime($oldDate));

                if (!empty($records)) {
                    foreach ($records as $record) {
                        $desc = preg_replace('/[\r\n]/', ' ', $record['description']);
                        $reports[] = [
                            'date' => $newDate,
                            'name' => $record['user']['name'],
                            'project' => $record['project']['title'],
                            'description' => $desc,
                            'hours' => (string)$record['hours'],
                            'department' => $user['department'] != null ? $user['department']['title'] : '',
                        ];
                    }
                } else {
                    $reports[] = [
                        'date' => $newDate,
                        'name' => $user['name'],
                        'project' => '',
                        'description' => '',
                        'hours' => 0,
                        'department' => $user['department'] != null ? $user['department']['title'] : '',
                    ];
                }
            }
            $date = date("Y-m-d", strtotime($oldDate . '+ 1 days'));
        }

        return Excel::download(new GeneralReportsExport($reports), 'Отчет.xlsx');
    }
}
