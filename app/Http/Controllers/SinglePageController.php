<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SinglePageController extends Controller
{
    public function index()
    {
        return view("landing");
    }

    public function download()
    {
        return view("index");
    }
}
