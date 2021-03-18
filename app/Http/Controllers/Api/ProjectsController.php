<?php

namespace App\Http\Controllers\Api;

use App\Filters\ProjectsFilter;
use App\Http\Controllers\Controller;
use App\Project;
use App\Sorting\ProjectsSort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{
    public function create(Request $request){
        $data = $request->post('data');
        $record = Project::where('title', $data['title'])->first();
        if($record !== null)
            return response()->json([
                'message' => 'Такая запись уже есть в базе данных',
                'type' => 'warning'
            ]);
        $record = Project::create($data);
        return response()->json([
            'message' => 'Запись успешно создана',
            'type' => 'success',
            'record' => $record
        ]);
    }

    public function showList(){
        $records = Project::orderBy('title')->limit(10)->get();
        return response()->json($records);
    }

    public function showRecentList(){
        $records = DB::table('records')->select('projects.*')
            ->join('projects', 'records.project_id', '=', 'projects.id')
            ->where('user_id', auth()->user()->id)
            ->groupBy(['projects.id'])
            ->orderByDesc(DB::raw('count(projects.id)'))->limit(10)->get()->toArray();

        return response()->json($records);
    }

    public function show(Request $request){
        $records = Project::query();

        $records = (new ProjectsFilter($records, $request))->apply();

        $records = (new ProjectsSort($records, $request))->apply();

        $records = $records->paginate($this->countRecords);

        return response()->json($records);
    }


    public function update(Request $request, $id){
        $data = $request->post('data');
        $record = Project::find($id);
        $record->title = $data['title'];
        $record->save();
        return response()->json([
            'message' => 'Запись успешно изменена',
            'type' => 'success',
            'record' => $record
        ]);
    }

    public function delete(Request $request, $id){
        Project::destroy($id);
        return response()->json([
            'message' => 'Запись успешно удалена',
            'type' => 'success',
        ]);
    }

    public function showSearch($query){
        $records = Project::where('title', 'like', $query.'%')->limit(10)->get();
        return response()->json($records);
    }
}
