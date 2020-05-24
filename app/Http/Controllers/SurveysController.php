<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SurveysController extends Controller
{
    public function index()
    {
        return view('surveys.index');
    }
}
