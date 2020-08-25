<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Payment;


use App\CustomerOrder;

use DB;


class IndividualPerformanceController extends Controller
{
    public function index(){
        session(['header_text' => 'Individual Performance']);

        return view('individual-performance.index');
    }

    public function indexData(){

        
        $companyId =  Auth::user()->company_id;
        $companyId =  '1';

        $month = date('m');
        $year = date('Y');

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        $users = User::
                with([
                    'schedules' => function($query) use($month,$year) {
                        $query->whereMonth('date', '=',  $month);
                        $query->whereYear('date', '=',  $year);
                        $query->where('type','1');
                        $query->orderBy('code','ASC');
                    },
                    'schedules.attendances',
                    'company',
                    'technicalSales'
                ])
                ->whereHas('schedules', function ($q) use($month,$year){
                    $q->whereMonth('date', '=',  $month);
                    $q->whereYear('date', '=', $year);
                    $q->where('type','1');
                })
                ->whereIn('id',$selected_user)
                ->get();

        if($users){
            $users_data = [];
            foreach($users as $k => $user){
                $users_data[$k]['use_id'] = $user['id'];
                $users_data[$k]['name'] = $user['name'];
                $users_data[$k]['company'] = $user['company'] ? $user['company']['name'] : "";
                $users_data[$k]['month_year'] = date('F', mktime(0, 0, 0, $month, 10)) . ' ' .$year;
                $total_schedule = 0;
                $total_visited = 0;
                $total_customer_order = 0;
                $total_customer_volume_order = 0;
                $total_customer_expenses = 0;
                if($user['schedules']){
                    $total_schedule = count($user['schedules']);
                    $users_data[$k]['total_schedule'] = $total_schedule;

                    $total_attendances = 0;
                    
                    $total_order = 0;
                    $total_volume_order = 0;

                    $last_schedule_code = '';
                    $first = true;
                    foreach($user['schedules'] as $visited){
                        if($visited['attendances']){
                            $total_attendances += 1;

                            if($first){

                                $last_schedule_code = $visited['code'];
                                $get_customer_order = CustomerOrder::where('customer_code','like','%'.$visited['code'].'%')
                                                                ->whereMonth('purchase_date',$month)
                                                                ->whereYear('purchase_date',$year)
                                                                ->count();
                                if($get_customer_order > 0){
                                    $total_order += 1;
                                    $total_volume_order += $get_customer_order;
                                }

                                $first = false;
                            }else{

                                if($last_schedule_code != $visited['code']){
                                    $get_customer_order = CustomerOrder::where('customer_code','like','%'.$visited['code'].'%')
                                                                ->whereMonth('purchase_date',$month)
                                                                ->whereYear('purchase_date',$year)
                                                                ->count();
                                    if($get_customer_order > 0){
                                        $total_order += 1;
                                        $total_volume_order += $get_customer_order;
                                    }

                                    $last_schedule_code = $visited['code'];
                                }
                                
                            }

                            
                        }

                        
                    }

                    $total_visited = $total_attendances;
                    $total_customer_order = $total_order;
                    $total_customer_volume_order = $total_volume_order;

                    $users_data[$k]['total_visited'] = $total_visited;
                    $users_data[$k]['total_customer_order'] = $total_customer_order;
                    $users_data[$k]['total_customer_volume_order'] = $total_customer_volume_order;
                    
                }else{
                    $users_data[$k]['total_schedule'] = $total_schedule;
                    $users_data[$k]['total_schedule'] = $total_visited;
                    $users_data[$k]['total_customer_order'] = $total_customer_order;
                    $users_data[$k]['total_customer_volume_order'] = $total_customer_volume_order;
                }

                $expenses = Payment::with('expense')
                                    ->whereHas('expense',function($q) use($month,$year)  {
                                        $q->whereMonth('created_at', '=',  $month);
                                        $q->whereYear('created_at', '=',  $year);
                                    })
                                    ->whereNotNull('document_code')
                                    ->where('user_id',$user['id'])
                                    ->get();

                if($expenses){
                    $total_expense = 0;
                    foreach($expenses as $expense){
                        $total_expense += $expense['expense']['amount'];
                    }
                    $total_customer_expenses = $total_expense;
                    $users_data[$k]['total_customer_expenses'] = $total_customer_expenses;
                }else{
                    $users_data[$k]['total_customer_expenses'] = $total_customer_expenses;
                }

                if($user['technicalSales']){                    
                    $users_data[$k]['monthly_qouta'] = $user['technicalSales'][0]['monthly_qouta'];
                }else{
                    $users_data[$k]['monthly_qouta'] = "";
                }

            }
        }

        return $users_data;

    }

    public function indexFilterData(Request $request){


        $request->validate([
            'company' => 'required'
        ]);

        $data = $request->all();
        $companyId =  $data['company'];

        $year = $data['year'] ? $data['year'] : date('Y');
        $month = $data['month'] ? $data['month'] : date('m');

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        $users = User::
                with([
                    'schedules' => function($query) use($month,$year) {
                        $query->whereMonth('date', '=',  $month);
                        $query->whereYear('date', '=',  $year);
                        $query->where('type','1');
                        $query->orderBy('code','ASC');
                    },
                    'schedules.attendances',
                    'company',
                    'technicalSales'
                ])
                ->whereHas('schedules', function ($q) use($month,$year){
                    $q->whereMonth('date', '=',  $month);
                    $q->whereYear('date', '=', $year);
                    $q->where('type','1');
                })
                ->whereIn('id',$selected_user)
                ->get();

        if($users){
            $users_data = [];
            foreach($users as $k => $user){
                $users_data[$k]['use_id'] = $user['id'];
                $users_data[$k]['name'] = $user['name'];
                $users_data[$k]['company'] = $user['company'] ? $user['company']['name'] : "";
                $users_data[$k]['month_year'] = date('F', mktime(0, 0, 0, $month, 10)) . ' ' .$year;
                $total_schedule = 0;
                $total_visited = 0;
                $total_customer_order = 0;
                $total_customer_volume_order = 0;
                $total_customer_expenses = 0;
                if($user['schedules']){
                    $total_schedule = count($user['schedules']);
                    $users_data[$k]['total_schedule'] = $total_schedule;

                    $total_attendances = 0;
                    
                    $total_order = 0;
                    $total_volume_order = 0;

                    $last_schedule_code = '';
                    $first = true;
                    foreach($user['schedules'] as $visited){
                        if($visited['attendances']){
                            $total_attendances += 1;

                            if($first){

                                $last_schedule_code = $visited['code'];
                                $get_customer_order = CustomerOrder::where('customer_code','like','%'.$visited['code'].'%')
                                                                ->whereMonth('purchase_date',$month)
                                                                ->whereYear('purchase_date',$year)
                                                                ->count();
                                if($get_customer_order > 0){
                                    $total_order += 1;
                                    $total_volume_order += $get_customer_order;
                                }

                                $first = false;
                            }else{

                                if($last_schedule_code != $visited['code']){
                                    $get_customer_order = CustomerOrder::where('customer_code','like','%'.$visited['code'].'%')
                                                                ->whereMonth('purchase_date',$month)
                                                                ->whereYear('purchase_date',$year)
                                                                ->count();
                                    if($get_customer_order > 0){
                                        $total_order += 1;
                                        $total_volume_order += $get_customer_order;
                                    }

                                    $last_schedule_code = $visited['code'];
                                }
                                
                            }

                            
                        }

                        
                    }

                    $total_visited = $total_attendances;
                    $total_customer_order = $total_order;
                    $total_customer_volume_order = $total_volume_order;

                    $users_data[$k]['total_visited'] = $total_visited;
                    $users_data[$k]['total_customer_order'] = $total_customer_order;
                    $users_data[$k]['total_customer_volume_order'] = $total_customer_volume_order;
                    
                }else{
                    $users_data[$k]['total_schedule'] = $total_schedule;
                    $users_data[$k]['total_schedule'] = $total_visited;
                    $users_data[$k]['total_customer_order'] = $total_customer_order;
                    $users_data[$k]['total_customer_volume_order'] = $total_customer_volume_order;
                }

                $expenses = Payment::with('expense')
                                ->whereHas('expense',function($q) use($month,$year)  {
                                    $q->whereMonth('created_at', '=',  $month);
                                    $q->whereYear('created_at', '=',  $year);
                                })
                                ->whereNotNull('document_code')
                                ->where('user_id',$user['id'])
                                ->get();

                if($expenses){
                    $total_expense = 0;
                    foreach($expenses as $expense){
                        $total_expense += $expense['expense']['amount'];
                    }
                    $total_customer_expenses = $total_expense;
                    $users_data[$k]['total_customer_expenses'] = $total_customer_expenses;
                }else{
                    $users_data[$k]['total_customer_expenses'] = $total_customer_expenses;
                }

                if($user['technicalSales']){                    
                    $users_data[$k]['monthly_qouta'] = $user['technicalSales'][0]['monthly_qouta'];
                }else{
                    $users_data[$k]['monthly_qouta'] = "";
                }
            }
        }

        return $users_data;

    }
    
}
