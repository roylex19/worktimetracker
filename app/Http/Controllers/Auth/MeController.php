<?php

namespace App\Http\Controllers\Auth;

use App\Department;
use App\Department_view;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeController extends Controller
{
    public function __construct(){
        $this->middleware(['auth:api']);
    }

    public function __invoke(Request $request){
        $user = $request->user();

        if($user->disabled)
            return;

        $dep = Department::where('id', $user->department_id)->first();
        $depsViews = Department_view::where('user_id', $user->id)->first() != null ? Department_view::where('user_id', $user->id)->first()['departments'] : [0];

        $user->department = $dep;
        $user->departmentsViews = $depsViews;

        return response()->json($user);
    }
}
