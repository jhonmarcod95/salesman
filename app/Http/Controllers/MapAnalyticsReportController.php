<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapAnalyticsReportController extends Controller
{
    //
    public function index(){

        session(['header_text' => 'Map Anaytics Report']);

        return view('map-analytics-report.index');
    }
}
