<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Survey;

class SurveysController extends Controller
{
    public function index()
    {
        return view('surveys.index');
    }

    public function surveyHome()
    {
        return view('surveys.home');
    }

    public function checkIfAlreadySurveyed($customer_id)
    {
        $survey = Survey::where('customer_id',$customer_id)
                        ->whereDate('created_at',Carbon::today())
                        ->where('user_id',Auth::user()->id)
                        ->exists();

        if($survey === true) {
            return 1;
        }

        return 0;
    }

    
}
