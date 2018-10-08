<?php

namespace App\Http\Controllers;

use App\TechnicalSalesRepresentative;
use Illuminate\Http\Request;

class TsrController extends Controller
{
    public function index(){
        session(['header_text' => 'Technical Sales Representative']);

        $tsrs = TechnicalSalesRepresentative::all();

        return view('tsr.index', compact(
            'tsrs'
        ));
    }

    public function create(){
        return view('tsr.create');
    }
}
