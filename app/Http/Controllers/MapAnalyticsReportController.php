<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\{
    Schedule,
    Customer
};

use \Carbon\Carbon;



class MapAnalyticsReportController extends Controller
{
    public function index(){

        session(['header_text' => 'Map Anaytics Report']);

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

    public function getYear(){
        $result = Schedule::select(DB::raw('YEAR(created_at) as year'))->whereNotNull('created_at')->distinct()->get();
        $years = $result->pluck('year');
        return $years;
    }
}