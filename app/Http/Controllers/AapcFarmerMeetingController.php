<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AapcFarmerMeetingController extends Controller
{
    public function index()
    {
        return view('aapc-farmer.index');
    }

    public function create()
    {
        return view('aapc-farmer.create');
    }
}
