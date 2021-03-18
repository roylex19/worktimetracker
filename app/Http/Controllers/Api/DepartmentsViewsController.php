<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Record;
use App\Project;
use App\User;
use App\Department;
use App\Department_view;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentsViewsController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->post();

        $depView = Department_view::where('user_id', $data['user']['id'])->first();

        if(isset($data['departments'])) {
            $deps = array_map(function ($v) {
                return $v['department']['id'];
            }, $data['departments']);
        }
        else{
            $deps = [];
        }

        if ($depView !== null) {
            $depView->update(['departments' => $deps]);
        } else {
            Department_view::create([
                'user_id' => $data['user']['id'],
                'departments' => $deps,
            ]);
        }

        return response()->json([
            'message' => 'Изменения сохранены',
            'type' => 'success',
        ]);
    }

    public function show($id)
    {
        $views = Department_view::where('user_id', $id)->get()->toArray();
        $records = [];
        foreach ($views as $view) {
            foreach ($view['departments'] as $department_id) {
                $dep = Department::find($department_id)->toArray();
                $records[] = $dep;
            }
        }
        return response()->json($records);
    }

    public function delete(Request $request, $id){
        Department::destroy($id);
        return response()->json([
            'message' => 'Запись успешно удалена',
            'type' => 'success',
        ]);
    }
}
