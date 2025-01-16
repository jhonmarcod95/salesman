<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Schedule;
use App\Attendance;

class SupportForcedCloseController extends Controller
{
    public function index(){
        return view('forced-close/index');
    }

    public function indexData(Request $request){

    }
}
