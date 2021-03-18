<?php

namespace App\Http\Controllers\Api;

use App\Filters\UsersFilter;
use App\Http\Controllers\Controller;
use App\Sorting\UsersSort;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function showList(){
        $records = User::with('department')
            ->where('name', '<>', 'Ауль Никита Игоревич')
            ->where('disabled', false)
            ->orderBy('name')
            ->get();
        return response()->json($records);
    }

    public function show(Request $request){
        $records = User::query();

        $records = (new UsersSort($records, $request))->apply();

        $records = (new UsersFilter($records, $request))->apply();

        $records = $records->with('department')->paginate($this->countRecords);

        return response()->json($records);
    }

    public function create(Request $request){
        $data = $request->post('data');

        if($data['phone'] !== null){
            $user = User::where('phone', $data['phone'])->first();
            if($user === null) {
                $record = User::create([
                    'name' => $data['name'],
                    'email' => isset($data['email']) ? $data['email'] : null,
                    'phone' => $data['phone'],
                    'department_id' => isset($data['department']['id']) ? $data['department']['id'] : null
                ]);
                $record->department = $data['department'];
                return response()->json([
                    'message' => 'Пользователь успешно создан',
                    'type' => 'success',
                    'record' => $record
                ]);
            }
            return response()->json([
                'message' => 'Пользователь с таким номером уже существует',
                'type' => 'warning',
            ]);
        }

        $user = User::where('name', $data['name'])->first();
        if($user === null) {
            $record = User::create([
                'name' => $data['name'],
                'email' => isset($data['email']) ? $data['email'] : null,
                'phone' => $data['phone'],
                'department_id' => isset($data['department']['id']) ? $data['department']['id'] : null
            ]);
            $record->department = $data['department'];
            return response()->json([
                'message' => 'Пользователь успешно создан',
                'type' => 'success',
                'record' => $record
            ]);
        }
        return response()->json([
            'message' => 'Пользователь с таким именем уже существует, необходимо ввести номер телефона',
            'type' => 'warning',
        ]);
    }

    public function update(Request $request, $id){
        $data = $request->post('data');
        $record = User::find($id);
        $record->name = $data['name'];
        $record->email = $data['email'];
        $record->phone = $data['phone'];
        $record->department_id = isset($data['department']['id']) ? $data['department']['id'] : null;
        $record->disabled = $data['disabled'];
        $record->save();
        $record->department = $data['department'];
        return response()->json([
            'message' => 'Запись успешно изменена',
            'type' => 'success',
            'record' => $record
        ]);
    }

    public function delete(Request $request, $id){
        User::destroy($id);
        return response()->json([
            'message' => 'Запись успешно удалена',
            'type' => 'success',
        ]);
    }

    public function updatePassword(Request $request, $id){
        $record = User::find($id);
        $record->password = bcrypt($request->password);
        $record->save();
        return response()->json([
            'message' => 'Пароль успешно изменен',
            'type' => 'success',
        ]);
    }

    public function updateAdminRule(Request $request, $id){
        $record = User::find($id);
        $record->admin = $request->admin;
        $record->save();
        return response()->json([
            'admin' => $record->admin,
            'message' => 'Права пользователя успешно изменены',
            'type' => 'success',
        ]);
    }

    public function deactivate($id){/*
        $record = User::find($id);
        $record->password = Hash::make(Str::random(10));
        $record->save();*/
        return response()->json([
            'message' => 'Пользователь успешно деактивирован',
            'type' => 'success',
        ]);
    }
}
