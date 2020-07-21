<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;


use App\CustomerOrder;
use App\SapUser;
use App\SapServer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class IndividualPerformanceController extends Controller
{
    public function index(){
        return view('individual-performance.index');
    }

    public function indexData(){


        $companyId =  Auth::user()->company_id;

        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        return $users = User::
                with([
                    'schedules' => function($query) use($startDate,$endDate) {
                        $query->where('date', '>=',  $startDate);
                        $query->whereDate('date', '<=',  $endDate);
                        $query->where('type','1');
                        $query->orderBy('date','ASC');
                    },
                    'schedules.attendances',
                    'schedules.customers',
                    
                ])
                ->whereHas('schedules', function ($q) use($startDate,$endDate){
                    $q->where('date', '>=',  $startDate);
                    $q->whereDate('date', '<=', $endDate);
                    $q->where('type','1');
                })
                ->whereIn('id',$selected_user)
                ->get();

    }

    public function customer_order(){

    }
}
