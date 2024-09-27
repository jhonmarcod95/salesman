<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoordinatorReportController extends Controller
{
    public function index() {
        return view('expense.coordinator-report');
    }
}
