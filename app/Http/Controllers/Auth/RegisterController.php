<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __invoke(Request $request){
        if(User::where('phone', $request->phone)->first()){
            return response()->json([
                'message' => 'Пользователь с таким номером уже существует',
                'type' => 'warning',
            ]);
        }

        $user = User::where('name', $request->lastname.' '.$request->firstname.' '.$request->middlename)->first();
        if($user == null){
            User::create([
                'phone' => $request->phone,
                'name' => $request->lastname.' '.$request->firstname.' '.$request->middlename,
                'department_id' => $request->department['id'],
                'password' => bcrypt($request->password)
            ]);
        }
        else{
            $user->phone = $request->phone;
            $user->password = bcrypt($request->password);
            $user->save();
        }

        return response()->json([
            'message' => 'Вы успешно зарегистрированы',
            'type' => 'success',
        ]);
    }
}
