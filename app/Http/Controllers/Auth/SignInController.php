<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SignInController extends Controller
{
    public function __invoke(Request $request){

        if(!$token = auth()->attempt($request->only('phone', 'password'))){
            return response(null);
        }

        return response()->json(compact('token'));
    }
}
