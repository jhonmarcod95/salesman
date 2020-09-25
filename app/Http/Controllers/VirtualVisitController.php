<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VirtualVisitController extends Controller
{
    public function index()
    {
        return view('virtual-visit.index');
    }
}
