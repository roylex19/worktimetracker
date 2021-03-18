<?php

namespace App\Http\Controllers\Api;

use App\Department;
use App\Filters\DepartmentsFilter;
use App\Http\Controllers\Controller;
use App\Sorting\DepartmentsSort;
use Illuminate\Http\Request;

class DepartmentsController extends Controller
{
    public function create(Request $request){
        $data = $request->post('data');
        $record = Department::where('title', $data['title'])->first();
        if($record !== null)
            return response()->json([
                'message' => 'Такая запись уже есть в базе данных',
                'type' => 'warning'
            ]);
        $record = Department::create($data);
        return response()->json([
            'message' => 'Запись успешно создана',
            'type' => 'success',
            'record' => $record
        ]);
    }

    public function showList(){
        $records = Department::orderBy('title')->get();

        return response()->json($records);
    }

    public function show(Request $request){
        $records = Department::query();

        $records = (new DepartmentsFilter($records, $request))->apply();

        $records = (new DepartmentsSort($records, $request))->apply();

        $records = $records->paginate($this->countRecords);

        return response()->json($records);
    }

    public function update(Request $request, $id){
        $data = $request->post('data');
        $record = Department::find($id);
        $record->title = $data['title'];
        $record->save();
        return response()->json([
            'message' => 'Запись успешно изменена',
            'type' => 'success',
            'record' => $record
        ]);
    }

    public function delete(Request $request, $id){
        Department::destroy($id);
        return response()->json([
            'message' => 'Запись успешно удалена',
            'type' => 'success',
        ]);
    }
}
