<?php

namespace App\Http\Controllers;

use App\CustomerClassification;
use App\Rules\GeocodeCustomerRule;
use App\Rules\GeocodeEventRule;
use Auth;
use Carbon;   
use App\Customer;
use App\Rules\TimeRule;
use App\Schedule;
use App\ScheduleTypes;
use App\TechnicalSalesRepresentative;
use App\Message;
use App\RequestSchedule;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Geocoder\Facades\Geocoder;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        session(['header_text' => 'Schedules']);

        $company_id = Auth::user()->companies->first()->id; //used to filter with same company

        $tsrs = TechnicalSalesRepresentative::select(
            DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'company_user.user_id')
            ->join('company_user', 'company_user.user_id', 'technical_sales_representatives.user_id')
            ->where('company_user.company_id', $company_id)
            ->pluck(
                'name',
                'company_user.user_id'
            );

        $customers = Customer::select(DB::raw("CONCAT(name, ' - ', street) AS name"), 'customer_code')
            ->where('company_id', $company_id)
            ->pluck(
                'name',
                'customer_code'
            );

        $scheduleTypes = ScheduleTypes::all()
            ->pluck(
                'description',
                'id')
            ->put('', '');

        $notification = Message::where('user_id', '!=', Auth::user()->id)->whereNull('seen')->count();

        return view('schedule.index', compact(
            'tsrs',
            'scheduleTypes',
            'customers',
            'notification'
        ));
    }

    public function indexData(Request $request, $date_from, $date_to){

        /* search specific user ***********************************/
        if($request->exists('tsrs')){
            $user_ids = $request->tsrs;
        }
        //search all
        else{
            $user_ids = [];
        }
        /* ********************************************************/

        /* search specific customer (sap based only) **************/
        if($request->exists('customer_codes')){
            $customer_codes = $request->customer_codes;
        }
        //search all
        else{
            $customer_codes = Schedule::whereBetween('date', [$date_from, $date_to])
                ->get()
                ->pluck('code');
        }
        /* *********************************************************/

        $schedules = Schedule::filter($date_from, $date_to, $user_ids, $customer_codes);
        return $schedules;
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'type' => 'required',
            'start_date' => 'required',
            'radius' => 'required',
            'end_date' => 'required|after_or_equal:start_date',
            'start_time' => [new TimeRule($request->start_time, $request->end_time), 'required'],
            'end_time' => 'required',
        ]);

        $schedule_type = $request->type;

        $schedule_code = Schedule::createScheduleCode($schedule_type); //for events and mapping customer

        $dates = $this->fetchDatePeriod($request->start_date, $request->end_date);

        DB::beginTransaction();
        foreach ($dates as $date){
            #Customer & Office Visit
            if($schedule_type == '1' || $schedule_type == '5'){

                $request->validate([
                    'customer_codes' => 'required',
                ]);

                $customer_codes = $request->customer_codes;

                foreach ($customer_codes as $customer_code){
                    $customer = Customer::where('customer_code', $customer_code)->first();

                    $schedule = new Schedule();
                    $schedule->user_id = $request->user_id;
                    $schedule->type = $schedule_type;
                    $schedule->code = $customer_code;
                    $schedule->name = $customer->name;
                    $schedule->address = $customer->street;
                    $schedule->date = $date;
                    $schedule->start_time = $request->start_time;
                    $schedule->end_time = $request->end_time;
                    $schedule->status = '2';
                    $schedule->remarks = $request->remarks;
                    $schedule->lat = $customer->lat;
                    $schedule->lng = $customer->lng;
                    $schedule->km_distance = $request->radius;
                    $schedule->save();

                    $data[] = $this->dataOutput($date,$date,$request->user_id,$schedule->code);
                }
            }
            #Event & Mapping
            else{

                $request->validate([
                    'name' => 'required|max:191',
                    'address' => ['required', 'max:191', new GeocodeEventRule()],
                ]);

                $geocode = Geocoder::getCoordinatesForAddress($request->address);

                $schedule = new Schedule();
                $schedule->user_id = $request->user_id;
                $schedule->type = $schedule_type;
                $schedule->code = $schedule_code;
                $schedule->name = $request->name;
                $schedule->address = $request->address;
                $schedule->date = $date;
                $schedule->start_time = $request->start_time;
                $schedule->end_time = $request->end_time;
                $schedule->status = '2';
                $schedule->remarks = $request->remarks;
                $schedule->lat = $geocode['lat'];
                $schedule->lng = $geocode['lng'];
                $schedule->km_distance = $request->radius;
                $schedule->save();

                $data[] = $this->dataOutput($date,$date,$request->user_id,$schedule->code);
            }
        }
        DB::commit();

        if($request->has('id')){
            RequestSchedule::where('id', $request->id)->update(array('isApproved' => 1));
        }

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required',
            'date' => 'required',
            'radius' => 'required',
            'start_time' => [new TimeRule($request->start_time, $request->end_time), 'required'],
            'end_time' => 'required',
        ]);

        $schedule_type = $request->type;

        #Customer Visit
        if($schedule_type == '1' || $schedule_type == '5'){
            $request->validate([
                'customer_code' => [new GeocodeCustomerRule(), 'required'],
            ]);

            $customer = Customer::where('customer_code', $request->customer_code)->first();

            $schedule = Schedule::find($id);
            $schedule->type = $schedule_type;
            $schedule->code = $customer->customer_code;
            $schedule->name = $customer->name;
            $schedule->address = $customer->town_city;
            $schedule->start_time = $request->start_time;
            $schedule->end_time = $request->end_time;
            $schedule->status = '2';
            $schedule->remarks = $request->remarks;
            $schedule->lat = $customer->lat;
            $schedule->lng = $customer->lng;
            $schedule->km_distance = $request->radius;
            $schedule->save();
        }
        #Event & Mapping
        else{
            $request->validate([
                'name' => 'required|max:191',
                'address' => ['required', 'max:191', new GeocodeEventRule()],
            ]);

            $geocode = Geocoder::getCoordinatesForAddress($request->address);

            $schedule = Schedule::find($id);
            $schedule->user_id = $request->user_id;
            $schedule->type = $request->type;
            $schedule->code = Schedule::createScheduleCode($schedule_type);
            $schedule->name = $request->name;
            $schedule->address = $request->address;
            $schedule->start_time = $request->start_time;
            $schedule->end_time = $request->end_time;
            $schedule->status = '2';
            $schedule->remarks = $request->remarks;
            $schedule->lat = $geocode['lat'];
            $schedule->lng = $geocode['lng'];
            $schedule->km_distance = $request->radius;
            $schedule->save();
        }

        $data = $this->dataOutput($request->date,$request->date,$request->user_id,$schedule->code);
        return response()->json($data);
    }

    public function change(Request $request, $id){
        $schedule = Schedule::find($id);
        $schedule->date = $request->date;
        $schedule->save();
        return $schedule;
    }

    public function destroy($id)
    {
        $schedule = Schedule::find($id);
        $schedule->delete();
        return $schedule;
    }

    private function dataOutput($dateFrom, $dateTo, $userId, $code){
        $data = collect(DB::select("CALL p_schedules('$dateFrom', '$dateTo', '$userId', '$code')"))->first();
        return $data;
    }

    /**
     * Get todays schedule (Customer, Event, Mapping)
     *
     * @return \Illuminate\Http\Response
     */

    public function todays(){

        if(Auth::user()->level() < 8){
            return Schedule::with('user','attendances')->whereHas('user' , function($q){
                $q->whereHas('companies', function ($q){
                    $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            })->whereNotIn('type', [4,5])->where('date', Carbon\Carbon::now()->toDateString())->get();
        }

        return Schedule::with('user','attendances')->whereNotIn('type', [4,5])->where('date', Carbon\Carbon::now()->toDateString())->get();
    }

    /**
     * Get todays all schedule
     *
     * @return \Illuminate\Http\Response
     */

    public function todaysAll(){

        if(Auth::user()->level() < 8){
            return Schedule::with('user','attendances')->whereHas('user' , function($q){
                $q->whereHas('companies', function ($q){
                    $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            }) ->where('date', Carbon\Carbon::now()->toDateString())->get();
        }

        return Schedule::with('user','attendances')->whereNotIn('type', [4,5])->where('date', Carbon\Carbon::now()->toDateString())->get();
    }

    /**
    * Get all todays schedule per user
     *
     * @return \Illuminate\Http\Response
     */

    public function todayByUser(){

         $schedule = Schedule::with('user','attendances')
            ->when(Auth::user()->level() < 8, function ($q) {
                $q->whereHas('user', function ($q){
                    $q->whereHas('companies', function($q){
                        $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                    });
                });
            })
            ->where('date', Carbon\Carbon::now()->toDateString())->get();
         
         return  array ($schedule->sortBy('user.name')->groupBy('user.name'));
    }


    /**
    *  show changed schedule 
     *
     * @return \Illuminate\Http\Response
     */
    public function changeScheduleIndex(){
        session(['header_text' => 'Change Schedule']);

        $notification = Message::where('user_id', '!=', Auth::user()->id)->whereNull('seen')->count();
        return view('schedule.change-schedule', compact('notification'));
    }

   /**
    * Get all request for changing of schedule
     *
     * @return \Illuminate\Http\Response
     */
    public function changeScheduleIndexData(Request $request){
        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);
        
        $request_status = $request->request_status;

        return RequestSchedule::with('user')
            ->when(Auth::user()->level() == 3 || Auth::user()->level() == 4 , function($q){
                $q->whereHas('user', function ($q){
                    $q->whereHas('companies', function($q){
                        $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                    });
                });
            })
            ->when($request_status || $request_status == '0'  , function($q) use ($request_status){
                $q->where('isApproved', $request_status);
            })
            ->whereDate('created_at', '>=',  $request->startDate)
            ->whereDate('created_at' ,'<=', $request->endDate)
            ->orderBy('id','desc')->get();
    }

     /**
    * Disapproving request for changing of schedule
     *
     * @return \Illuminate\Http\Response
     */

    public function changeScheduleDisapproved(Request $request){

        $request->validate([
            'id' => 'required'
        ]);

        return RequestSchedule::where('id', $request->id)->update(array('isApproved' => 2));
    }

    function fetchDatePeriod($startDate, $endDate){

        $end = date_create($endDate);
        date_add($end,date_interval_create_from_date_string("1 days"));
        $end = date_format($end,"Y-m-d");

        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            new DateTime($end)
        );

        foreach ($period as $key => $value) {
            $result[] = $value->format('Y-m-d');
        }

        return $result;
    }

    function haversineGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    function scheduleCustomerData($classification){

        $company_id = Auth::user()->companies->first()->id; //filter by auth company

        ################################
        $customers = Customer::select(DB::raw("CONCAT(name, ' - ', street) AS name"), 'customer_code')
            ->when($classification != 'null', function ($q) use($classification){
                $q->where('classification', $classification);
            })
            ->where('company_id', $company_id)
            ->get([
                'name',
                'customer_code'
            ]);
        ################################

        return $customers;

    }
}
