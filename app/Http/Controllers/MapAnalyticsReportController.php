<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\{
    Attendance,
    Schedule,
    ScheduleTypes,
    Customer,
    User
};

use \Carbon\Carbon;



class MapAnalyticsReportController extends Controller
{
    public function index(){
        session(['header_text' => 'Map Analytics Report']);
        return view('map-analytics-report.index');
    }

    public function customersData(){
        return Customer::orderBy('id', 'desc')->get();
    }

    public function usersData(Request $request){

        $customer_codes = [];
        if($request->customerSelect){
            foreach ($request->customerSelect as $customer_code){
               array_push($customer_codes,$customer_code['customer_code']);
            }
        }

        $select_month = $request->selectMonth;
        $select_year = $request->selectYear;

        $schedules = Schedule::with('attendances', 'user')
                    ->when($request->selectMonth, function($q) use($select_month) {
                        $q->whereMonth('date', '=', $select_month);
                    })
                    ->when($request->selectYear, function($q) use($select_year) {
                        $q->whereYear('date', '=', $select_year);
                    })
                    ->when(!empty($customer_codes), function($q) use($customer_codes) {
                        $q->whereIn('code', $customer_codes);
                    })
                    ->where('type', '1')
                    ->where('status', '1')
                    ->orderBy('date','ASC')
                    ->get();

        
        $features = [];
        if(count($schedules) > 0){
           
            foreach ($schedules as $schedule){
                $name = $schedule->user['name'] ? $schedule->user['name'] : '';
                $features[] = '
                {
                    "type": "Feature",
                    "geometry": {
                        "type": "Point",
                        "coordinates": [' . $schedule->attendances->sign_in_longitude  . ', ' . $schedule->attendances->sign_in_latitude . ']
                    },
                    "properties": {
                        "id": "' . $schedule->attendances->id . '",
                        "name": "' .  $name . '",
                        "sign_in": "' . $schedule->attendances->sign_in . '",
                        "sign_out": "' . $schedule->attendances->sign_out . '",
                        "remarks": "' . $schedule->attendances->remarks . '",
                        "sign_in_image": "' . $schedule->attendances->sign_in_image . '",
                        "sign_out_image": "' . $schedule->attendances->sign_out_image . '",
                        "schedule_code": "' . $schedule->code . '",
                        "schedule_name": "' . $schedule->name . '",
                        "schedule_address": "' . $schedule->address . '",
                        "schedule_date": "' . $schedule->date . '",
                        "schedule_start_time": "' . $schedule->start_time . '",
                        "schedule_end_time": "' . $schedule->end_time . '"
                    }
                }
                ';
            }
            $features = implode(',', $features);   
        }

        $map_markers = '
        {
            "type": "FeatureCollection",
            "features": [' . $features . ']
        }
        ';
    
        return json_decode($map_markers, true);
        

    }

    public function mapUsers(){
        return view('map-analytics-report.map_users');
    }

    public function mapCustomers(){
        return view('map-analytics-report.map_customers');
    }

    public function users(){

        if(Auth::user()->roles[0]->slug == 'it'|| Auth::user()->roles[0]->slug == 'audit' || Auth::user()->roles[0]->slug == 'president'){
            return User::with('roles','company')
                    ->whereHas('roles', function ($query) {
                        $query->whereIn('slug', ['unverified','user','admin','audit','hr','tax','approver','tsr','ap','manager','coordinator','avp','vp','evp','president','it']);
                    })
                    ->whereHas('company', function ($query) {
                        $query->where('id', '=', Auth::user()->company_id);
                    })
                    ->orderBy('name', 'ASC')
                    ->get();
        }
        else{
            return User::with('roles','company')
                        ->whereHas('roles', function ($query) {
                            $query->whereIn('slug', ['unverified','user','admin','audit','hr','tax','approver','tsr','ap','manager','coordinator','avp','vp','evp','president','it']);
                        })
                        ->whereHas('company', function ($query) {
                            $query->where('id', '=', Auth::user()->company_id);
                        })
                        ->orderBy('name', 'ASC')
                        ->get();
        }
    }

    public function scheduleTypes(){
        return ScheduleTypes::orderBy('id', 'desc')->get();
    }

    public function userLocations(Request $request){

        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);
        $default_user_ids = [];
        if($request->defaultUsers){
            foreach ($request->defaultUsers as $id){
                array_push($default_user_ids,$id['id']);
             }
        }

        $selected_user_id = $request->userId;
        $schedule_type = $request->scheduleType;
        $searchAddress = $request->searchAddress;
        $schedules = Schedule::with('attendances','user','schedule_type')
                    ->when(!empty($request->userId), function($q) use($selected_user_id) {
                        $q->where('user_id',  $selected_user_id);
                    })
                    ->when(empty($request->userId), function($q) use($default_user_ids) {
                        $q->whereIn('user_id',  $default_user_ids);
                    })
                    ->when(!empty($request->scheduleType), function($q) use($schedule_type) {
                        $q->where('type',  $schedule_type);
                    })
                    ->when(!empty($request->searchAddress), function($q) use($searchAddress) {
                        $q->where('address',  'like', '%'.$searchAddress.'%');
                    })
                    ->where('date', '>=',  $request->startDate)
                    ->whereDate('date' ,'<=', $request->endDate)
                    ->where('status', '1')
                    ->orderBy('date', 'desc')->get();
        
        if($schedules){
            return $schedules;
        }
        else{
            return 'Empty';
        }

        
    }

    public function customerLocations(Request $request){
        
        ini_set('memory_limit','1024M');

        $request->validate([
            'selectedCompanies' => 'required',
        ]);

        $selected_classification_ids = [];
        if($request->selectedClassifications){
            foreach ($request->selectedClassifications as $id){
                array_push($selected_classification_ids,$id['id']);
             }
        }

        $selected_company_ids = [];
        if($request->selectedCompanies){
            foreach ($request->selectedCompanies as $id){
                array_push($selected_company_ids,$id['id']);
             }
        }
        
        $selected_status_ids = [];
        if($request->selectedStatuses){
            foreach ($request->selectedStatuses as $id){
                array_push($selected_status_ids,$id['id']);
             }
        }

        $date_period['date_from'] = $request->date_period_from ? date('Y-m-d',strtotime($request->date_period_from)) : date('Y-m-d');
        $date_period['date_to'] =  $request->date_period_to ? date('Y-m-d',strtotime($request->date_period_to)) : date('Y-m-d');

        $selected_province_id = $request->selectedProvince ? $request->selectedProvince['id'] : '';
       
        $customers = Customer::with(array('classifications','statuses','provinces','visits' => function($q) use($date_period){
                            $q->where('date','<=' , $date_period['date_from']);
                            $q->where('date','>=' , $date_period['date_to']);
                    }))
                    ->when(!empty($request->selectedClassifications), function($q) use($selected_classification_ids) {
                        $q->whereIn('classification',  $selected_classification_ids);
                    })
                    ->when(!empty($request->selectedCompanies), function($q) use($selected_company_ids) {
                        $q->whereIn('company_id',  $selected_company_ids);
                    })
                    ->when(!empty($request->selectedStatuses), function($q) use($selected_status_ids) {
                        $q->whereIn('status',  $selected_status_ids);
                    })
                    ->when(!empty($request->selectedProvince), function($q) use($selected_province_id) {
                        $q->where('province_id',  $selected_province_id);
                    })
                    ->orderBy('classification', 'ASC')->get();
        if($customers){
            return $customers;
        }
        else{
            return 'Empty';
        }
    }

    public function getYear(){
        $result = Schedule::select(DB::raw('YEAR(created_at) as year'))->whereNotNull('created_at')->distinct()->get();
        $years = $result->pluck('year');
        return $years;
    }

    public function customerVisits($customer_code){
        return Schedule::with('attendances', 'user')
                    
                    ->where('code', $customer_code)
                    ->where('type', '1')
                    ->where('status', '1')
                    ->orderBy('date','ASC')
                    ->get();
    }

}
