<?php

namespace App\Http\Controllers;

use App\TechnicalSalesRepresentative;
use Illuminate\Http\Request;

class TsrController extends Controller
{
    /**
     *  Display tsr index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        session(['header_text' => 'Technical Sales Representative']);

        $tsrs = TechnicalSalesRepresentative::all();

        return view('tsr.index', compact(
            'tsrs'
        ));
    }

    /**
     * Get all tsr
     *
     * @return \Illuminate\Http\Response
     */

    public function indexData(){
        return  TechnicalSalesRepresentative::orderBy('id','desc')->get();
    }

    public function create(){
        return view('tsr.create');
    }
}
