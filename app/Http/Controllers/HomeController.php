<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

use App\Message;
use App\Schedule;
use App\Customer;
use App\User;
use App\Attendance;
use App\SAPServer;
use App\CustomerCode;
use App\CustomerCompany;
use App\CustomerSaleArea;
use App\Division;
use App\Company;
use App\DashboardFilter;


use App\SapUser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->hasRole(['hr','ap','tax','audit'])){
            session(['header_text' => 'Dashboard']);

            $message = Message::where('user_id', '!=', Auth::user()->id)->get();
            $notification = 0;  
            foreach($message as $notif){
    
                $ids = collect(json_decode($notif->seen, true))->pluck('id');
                if(!$ids->contains(Auth::user()->id)){
                    $notification++;
                }
            }
    
            return view('home',compact('notification'));
        }elseif(Auth::user()->hasRole('ap')){
            session(['header_text' => 'Payment']);

            return view('payment.index');

        }elseif(Auth::user()->hasRole(['tax', 'audit'])){
            session(['header_text' => 'Payment Posted']);

            return view('payment-posted.index');
        }else{
            session(['header_text' => 'Attendance Report']);

            return view('attendance-report.index');
        }

    }

    public function dashboard(){
        session(['header_text' => 'Dashboard']);
        return view('dashboard');
    }

    public function scheduleForVisit(){
        $date_today = date('Y-m-d');

        $user_id = Auth::user()->id;
        $dashboard_filter = DashboardFilter::where('user_id', $user_id)->first();
        if($dashboard_filter){
            $companyId = $dashboard_filter['company'] ? $dashboard_filter['company'] : Auth::user()->companies[0]->id;
        }else{
            $companyId = Auth::user()->companies[0]->id;
        }

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        return $schedule_for_visit = Schedule::with('attendances','user','user.companies','customer.provinces.regions')
                                                ->whereIn('user_id', $selected_user)
                                                ->where('date', '=' , $date_today)
                                                ->where('type','1')
                                                ->orderBY('name','ASC')
                                                ->get();
    }

    public function totalTimeTravel(){
        $date_today = date('Y-m-d');
        $params = [];
        $params['startDate'] = $date_today;
        $params['endDate'] = $date_today;

        $user_id = Auth::user()->id;
        
        $dashboard_filter = DashboardFilter::where('user_id', $user_id)->first();
        
        if($dashboard_filter){
            $companyId = $dashboard_filter['company'] ? $dashboard_filter['company'] : Auth::user()->companies[0]->id;
        }else{
            $companyId = Auth::user()->companies[0]->id;
        }

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }
        
        return $users_schedules_data = User::with(['schedules' => function ($query) use($params) {
                                        $query->with('attendances');
                                        $query->leftjoin('attendances',function($query){
                                            $query->on('attendances.schedule_id','=','schedules.id');
                                        });
                                        $query->where('date', '>=', $params['startDate']);
                                        $query->where('date', '<=', $params['endDate']);
                                        $query->select('attendances.sign_in','schedules.*');
                                        $query->orderBy('sign_in','ASC');
                                    }])
                                    ->whereIn('id',$selected_user)
                                    ->get();
        
    }

    public function monthlyTotalTimeTravel(){
        $date_today = date('Y-m-d');
        $params = [];
        $params['startDate'] = date('Y-m-01');
        $params['endDate'] = date('Y-m-d');
        
        $user_id = Auth::user()->id;
        
        $dashboard_filter = DashboardFilter::where('user_id', $user_id)->first();
        
        if($dashboard_filter){
            $companyId = $dashboard_filter['company'] ? $dashboard_filter['company'] : Auth::user()->companies[0]->id;
        }else{
            $companyId = Auth::user()->companies[0]->id;
        }

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }
        
        return $users_schedules_data = User::with(['schedules' => function ($query) use($params) {
                                        $query->with('attendances');
                                        $query->leftjoin('attendances',function($query){
                                            $query->on('attendances.schedule_id','=','schedules.id');
                                        });
                                        $query->where('date', '>=', $params['startDate']);
                                        $query->where('date', '<=', $params['endDate']);
                                        $query->select('attendances.sign_in','schedules.*');
                                        $query->orderBy('sign_in','ASC');
                                    }])
                                    ->whereIn('id',$selected_user)
                                    ->get();
        
    }

    public function monthlyActualVisited(){
        $date_today = date('Y-m-d');

        $user_id = Auth::user()->id;
        $dashboard_filter = DashboardFilter::where('user_id', $user_id)->first();
        if($dashboard_filter){
            $companyId = $dashboard_filter['company'] ? $dashboard_filter['company'] : Auth::user()->companies[0]->id;
        }else{
            $companyId = Auth::user()->companies[0]->id;
        }

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        $current_month_from = date('Y-m-01');
        $current_month_to = date('Y-m-t');

        return $current_month = Schedule::with('attendances')
                                                ->whereIn('user_id', $selected_user)
                                                ->where('date', '>=' , $current_month_from)
                                                ->whereDate('date', '<=' , $current_month_to)
                                                ->whereIn('type',['1','2'])
                                                ->orderBy('user_id','ASC')
                                                ->get();
        
    }

    public function mostCustomerVisits(){

        $user_id = Auth::user()->id;
        $dashboard_filter = DashboardFilter::where('user_id', $user_id)->first();
        if($dashboard_filter){
            $companyId = $dashboard_filter['company'] ? $dashboard_filter['company'] : Auth::user()->companies[0]->id;
        }else{
            $companyId = Auth::user()->companies[0]->id;
        }

        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }
        $users = User::
                with([
                    'schedules' => function($query) use($startDate,$endDate) {
                        $query->where('date', '>=',  $startDate);
                        $query->whereDate('date', '<=',  $endDate);
                        $query->where('type','1');
                        $query->orderBy('date','ASC');
                    },
                    'schedules.attendances',
                    'company',
                    'payments.expense' => function($q) use($startDate,$endDate) {
                        $q->where('created_at', '>=',  $startDate);
                        $q->whereDate('created_at', '<=',  $endDate);
                    }
                ])
                ->whereHas('schedules', function ($q) use($startDate,$endDate){
                    $q->where('date', '>=',  $startDate);
                    $q->whereDate('date', '<=', $endDate);
                    $q->where('type','1');
                })
                ->whereHas('payments', function ($q){
                    $q->whereNotNull('document_code');
                })
                ->whereIn('id',$selected_user)
                ->get();

        $user_arr = [];

        if($users){
            foreach($users as $k => $user){
                $user_arr[$k]['id'] =   $user['id'];
                $user_arr[$k]['name'] =   $user['name'];
                $user_arr[$k]['company'] = $user['company']['name'];

                //Schedules
                if($user['schedules']){
                    
                    $total_minutes = 0;
                    $x = 0;

                    $first = true;
                    $last_schedule_date = '';
                    $visit_per_day = [];
                    $xx = 0;
                    $count_visit_per_day = 1;
                    foreach($user['schedules'] as $schedule){
                        if($first){
                            $last_schedule_date = $schedule['date'];

                            if($schedule['attendances']){
                                $visit_per_day[$xx]['date'] = $schedule['date'];
                                $count_visit_per_day += 1;
                                $visit_per_day[$xx]['count'] = $count_visit_per_day;
                            }else{
                                $visit_per_day[$xx]['date'] = $schedule['date'];
                                $visit_per_day[$xx]['count'] = 0;
                            }
                            $first = false;
                        }else{
                            if($last_schedule_date == $schedule['date']){
                                if($schedule['attendances']){
                                    $count_visit_per_day += 1;
                                    $visit_per_day[$xx]['date'] = $schedule['date'];
                                    $visit_per_day[$xx]['count'] = $count_visit_per_day;
                                }else{
                                    $visit_per_day[$xx]['date'] = $schedule['date'];
                                    $visit_per_day[$xx]['count'] = 0;
                                }
                                
                                $last_schedule_date = $schedule['date'];
                            }else{
                                $xx += 1;
                               
                                if($schedule['attendances']){
                                    $count_visit_per_day += 1;
                                    $visit_per_day[$xx]['date'] = $schedule['date'];
                                    $visit_per_day[$xx]['count'] = 1;
                                }
                                $last_schedule_date = $schedule['date'];
                            }
                        }


                        if( $schedule['attendances']){
                            $x+=1;
                            $to_time = $schedule['attendances']['sign_in'] ? strtotime($schedule['attendances']['sign_in']) : "";
                            $from_time = $schedule['attendances']['sign_out'] ? strtotime($schedule['attendances']['sign_out']) : "";
                            $min_duration = round(abs($to_time - $from_time) / 60,2);

                            $total_minutes += $min_duration;
                        }
                    }

                    $user_arr[$k]['total_count_schedule'] =  count($user['schedules']);
                    $user_arr[$k]['total_count_visit'] =  $x;
                    $user_arr[$k]['total_customer_dwell_time'] =  $total_minutes;
                    $user_arr[$k]['average_time_per_visit'] =  $total_minutes ? $total_minutes / $x : 0;
                    $user_arr[$k]['average_visit_per_day'] = round(abs($count_visit_per_day / count($visit_per_day)),0);
                }
                else{
                    $user_arr[$k]['total_count_schedule'] =  0;
                    $user_arr[$k]['total_count_visit'] =  0;
                    $user_arr[$k]['total_count_vitotal_customer_dwell_timesit'] =  0;
                    $user_arr[$k]['average_time_per_visit'] =  0;
                    $user_arr[$k]['average_visit_per_day'] =  0;
                }

                //Expenses
                if($user['payments']){
                    $total_expenses = 0;
                    $first = true;
                    $last_schedule_date = '';

                    $expenses_data = [];
                    $x = 1;
                    foreach($user['payments'] as $z => $expenses){
                        if($first){
                            $last_schedule_date = $expenses['expense']['created_at'];
                            $first = false;
                        }else{
                            if($last_schedule_date != $expenses['expense']['created_at']){
                                $x++;
                                $last_schedule_date = $expenses['expense']['created_at'];
                            }
                        }
                        $total_expenses += $expenses['expense']['amount'];
                    }

                    $user_arr[$k]['average_expense_per_day'] =  round(abs($total_expenses / $x),0);

                }else{
                    $user_arr[$k]['average_expense_per_day'] =  0;
                }
            }

            array_multisort(array_column($user_arr, 'total_count_visit'), SORT_DESC, $user_arr);
        }

        return $user_arr;
    }

    public function workStartEndTime(){

        $user_id = Auth::user()->id;
        $dashboard_filter = DashboardFilter::where('user_id', $user_id)->first();
        if($dashboard_filter){
            $companyId = $dashboard_filter['company'] ? $dashboard_filter['company'] : Auth::user()->companies[0]->id;
        }else{
            $companyId = Auth::user()->companies[0]->id;
        }
        

        $date_today = date('Y-m-d');

        $date_yesterday = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_today) ) ));

        $yesterday_attendance_start = Attendance::with('user')
                                        ->whereHas('user',function($q) use($companyId) {
                                            $q->where('company_id', $companyId);
                                        })
                                        ->whereDate('sign_in',$date_yesterday)
                                        ->whereNotNull('sign_in')
                                        ->orderBy('sign_in','ASC')
                                        ->first();

        $yesterday_attendance_end = Attendance::with('user')
                                        ->whereHas('user',function($q) use($companyId) {
                                            $q->where('company_id', $companyId);
                                        })
                                        ->whereDate('sign_out',$date_yesterday)
                                        ->whereNotNull('sign_out')
                                        ->orderBy('sign_out','DESC')
                                        ->first();

        $today_attendance_start = Attendance::with('user')
                                        ->whereHas('user',function($q) use($companyId) {
                                            $q->where('company_id', $companyId);
                                        })
                                        ->whereDate('sign_in',$date_today)
                                        ->whereNotNull('sign_in')->orderBy('sign_in','ASC')
                                        ->first();


        $data = [];

        if($yesterday_attendance_start){
            $data['yesterday_attendance_start'] = date('F d, Y h:m A',strtotime($yesterday_attendance_start['sign_in']));
        }else{
            $data['yesterday_attendance_start'] = "";
        }

        if($yesterday_attendance_start){
            $data['yesterday_attendance_end'] = date('F d, Y h:m A',strtotime($yesterday_attendance_end['sign_out']));
        }else{
            $data['yesterday_attendance_end'] = "";
        }

        if($today_attendance_start){
            $data['today_attendance_start'] = date('F d, Y h:m A',strtotime($today_attendance_start['sign_in']));
        }else{
            $data['today_attendance_start'] = "";
        }

        return $data;
    }

    public function saveDashboardFilter(Request $request){
    
        $request->validate([
            'selectedCompanies' => 'required',
        ]);
        
        $company_id = $request->selectedCompanies;

        $user_id = Auth::user()->id;
        $validate_user = DashboardFilter::where('user_id', $user_id)->first();

        if($validate_user){
            $data = [];
            $data['company'] = $company_id;

            // return $data;
            $validate_user->update($data);
            return DashboardFilter::where('user_id', $user_id)->first();
        }else{
            $data = [];
            $data['user_id'] = $user_id;
            $data['company'] = $company_id;
            DashboardFilter::create($data);
            return DashboardFilter::where('user_id', $user_id)->first();
        }
        
    }

    public function getDashboardFilter(){

        $user_id = Auth::user()->id;
        $dashboard_filter = DashboardFilter::where('user_id', $user_id)->first();

        $dashboard_filter_data = [];
        $dashboard_filter_data['company'] = Company::where('id',$dashboard_filter['company'])->first();

        return $dashboard_filter_data;
    }

    public function getSapTsr(){

        $client = new Client();

        $connection = [
            'ashost' => '172.17.2.37',
            'sysnr' => '01',
            'client' => '778',
            'user' => 'payproject',
            'passwd' => 'welcome69+'
        ];

        $available_budget_headers = $client->request('GET', 'http://10.97.70.51:8012/api/read-table',
            ['query' => 
                ['connection' => $connection,
                    'table' => [
                        'table' => ['COSP' => 'available_budget'],
                        'fields' => [
                            'OBJNR' => 'OBJNR',
                            'GJAHR' => 'GJAHR',
                            'VERSN' => 'VERSN',
                            'WTG001' => 'WTG001',
                            'WTG002' => 'WTG002',
                            'WTG003' => 'WTG003',
                            'WTG004' => 'WTG004',
                            'WTG005' => 'WTG005',
                            'WTG006' => 'WTG006',
                            'WTG007' => 'WTG007',
                            'WTG009' => 'WTG009',
                            'WTG010' => 'WTG010',
                            'WTG011' => 'WTG011',
                            'WTG012' => 'WTG012',
                            'WTG013' => 'WTG013',
                            'WTG014' => 'WTG014',
                            'WTG015' => 'WTG015',
                        ],
                        'options' => [
                            ['TEXT' => "GJAHR = '2020' AND "],
                            ['TEXT' => "OBJNR = 'ORL60010007149'"]
                        ],
                    ]
                ]
            ],
            ['timeout' => 60],
            ['delay' => 10000]
        );

        return $available_budget = json_decode($available_budget_headers->getBody(), true); 

    }


    public function getSapTsrActualExpense(){

        $client = new Client();

        $connection = [
            'ashost' => '172.17.2.37',
            'sysnr' => '00',
            'client' => '100',
            'user' => 'payproject',
            'passwd' => 'welcome69+'
        ];

        $available_budget_headers = $client->request('GET', 'http://10.97.70.51:8012/api/read-table',
            ['query' => 
                ['connection' => $connection,
                    'table' => [
                        'table' => ['ZCOT_COSPACT' => 'actual_expenses'],
                        'fields' => [
                            'DMBTR' => 'DMBTR', 
                            'AUFNR' => 'AUFNR',
                            'GJAHR' => 'GJAHR',
                            'MONAT' => 'MONAT',
                            'VERSN' => 'VERSN',
                        ],
                        'options' => [
                            // ['TEXT' => "GJAHR = '2020' AND "],
                            // ['TEXT' => "AUFNR = 'L60010007164'"],
                            ['TEXT' => "AUFNR = 'L60010007149'"],
                            // ['TEXT' => "MONAT = '02'"],
                        ],
                    ]
                ]
            ],
            ['timeout' => 60],
            ['delay' => 10000]
        );

        return $available_budget = json_decode($available_budget_headers->getBody(), true); 

    }
    
    public function customerVisiterPerArea(){

        $date_today = date('Y-m-d');
        $params['startDate'] = date('Y-m-01');
        $params['endDate'] = $date_today;

        $user_id = Auth::user()->id;
        $dashboard_filter = DashboardFilter::where('user_id', $user_id)->first();
        if($dashboard_filter){
            $companyId = $dashboard_filter['company'] ? $dashboard_filter['company'] : Auth::user()->companies[0]->id;
        }else{
            $companyId = Auth::user()->companies[0]->id;
        }

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        //Luzon Regions
        $luzon_regions = [1,2,3,4,5,6,16,17];
        $luzon = Schedule::with('attendances','customer.provinces.regions')
                                                ->whereIn('user_id', $selected_user)
                                                ->where('date', '>=' , $params['startDate'])
                                                ->whereDate('date', '<=' , $params['endDate'])
                                                ->whereHas('customer.provinces.regions', function($q) use($luzon_regions){
                                                    $q->whereIn('id',$luzon_regions);
                                                })
                                                ->has('attendances')
                                                ->where('type','1')
                                                ->orderBY('name','ASC')
                                                ->count();

        //Visayas Regions
        $visayas_regions = [7,8,9];
        $visayas = Schedule::with('attendances','customer.provinces.regions')
                                                ->whereIn('user_id', $selected_user)
                                                ->where('date', '>=' , $params['startDate'])
                                                ->whereDate('date', '<=' , $params['endDate'])
                                                ->whereHas('customer.provinces.regions', function($q) use($visayas_regions){
                                                    $q->whereIn('id',$visayas_regions);
                                                })
                                                ->has('attendances')
                                                ->where('type','1')
                                                ->orderBY('name','ASC')
                                                ->count();
        
        //Mindanao Regions
        $mindanao_regions = [10,11,12,13,14,15];
        $mindanao = Schedule::with('attendances','customer.provinces.regions')
                                                ->whereIn('user_id', $selected_user)
                                                ->where('date', '>=' , $params['startDate'])
                                                ->whereDate('date', '<=' , $params['endDate'])
                                                ->whereHas('customer.provinces.regions', function($q) use($mindanao_regions){
                                                    $q->whereIn('id',$mindanao_regions);
                                                })
                                                ->has('attendances')
                                                ->where('type','1')
                                                ->orderBY('name','ASC')
                                                ->count();                                        

        return $data = [$luzon,$visayas,$mindanao];
    }

    public function customerSchedulePerArea(){

        $date_today = date('Y-m-d');
        $params['startDate'] = date('Y-m-01');
        $params['endDate'] = $date_today;

        $user_id = Auth::user()->id;
        $dashboard_filter = DashboardFilter::where('user_id', $user_id)->first();
        if($dashboard_filter){
            $companyId = $dashboard_filter['company'] ? $dashboard_filter['company'] : Auth::user()->companies[0]->id;
        }else{
            $companyId = Auth::user()->companies[0]->id;
        }

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        //Luzon Regions
        $luzon_regions = [1,2,3,4,5,6,16,17];
        $luzon = Schedule::with('attendances','customer.provinces.regions')
                                                ->whereIn('user_id', $selected_user)
                                                ->where('date', '>=' , $params['startDate'])
                                                ->whereDate('date', '<=' , $params['endDate'])
                                                ->whereHas('customer.provinces.regions', function($q) use($luzon_regions){
                                                    $q->whereIn('id',$luzon_regions);
                                                })
                                                ->where('type','1')
                                                ->orderBY('name','ASC')
                                                ->count();

        //Visayas Regions
        $visayas_regions = [7,8,9];
        $visayas = Schedule::with('customer.provinces.regions')
                                                ->whereIn('user_id', $selected_user)
                                                ->where('date', '>=' , $params['startDate'])
                                                ->whereDate('date', '<=' , $params['endDate'])
                                                ->whereHas('customer.provinces.regions', function($q) use($visayas_regions){
                                                    $q->whereIn('id',$visayas_regions);
                                                })
                                                ->where('type','1')
                                                ->orderBY('name','ASC')
                                                ->count();
        
        //Mindanao Regions
        $mindanao_regions = [10,11,12,13,14,15];
        $mindanao = Schedule::with('customer.provinces.regions')
                                                ->whereIn('user_id', $selected_user)
                                                ->where('date', '>=' , $params['startDate'])
                                                ->whereDate('date', '<=' , $params['endDate'])
                                                ->whereHas('customer.provinces.regions', function($q) use($mindanao_regions){
                                                    $q->whereIn('id',$mindanao_regions);
                                                })
                                                ->where('type','1')
                                                ->orderBY('name','ASC')
                                                ->count();                                        

        return $data = [$luzon,$visayas,$mindanao];
    }

    public function yearOptions(){
        $start_year = 2017;
        $current_year = date('Y');

        $year_data = [];
        $x = 0;
        for($start_year;$start_year <= $current_year;$start_year++){
            $year_data[$x]['id'] = $start_year;
            $year_data[$x]['name'] = $start_year;
            $x++;
        }

        array_multisort(array_map(function($year_data) {
            return $year_data['id'];
        }, $year_data), SORT_DESC, $year_data);

        return $year_data;
    }

}
