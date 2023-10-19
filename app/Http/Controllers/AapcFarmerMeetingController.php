<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AapcFarmerMeetingController extends Controller
{
    public function create()
    {
        return view('aapc-farmer.create');
    }
}
