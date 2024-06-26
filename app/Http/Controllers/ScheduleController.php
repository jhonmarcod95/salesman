<?php

namespace App\Http\Controllers;

use App\CustomerClassification;
use App\Rules\AllowedScheduler;
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
use App\User;
use App\Audit;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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

        $company_id = Auth::user()->companies->pluck(['id']); //used to filter with same company

        $tsrs = TechnicalSalesRepresentative::select(
            DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'company_user.user_id')
            ->join('company_user', 'company_user.user_id', 'technical_sales_representatives.user_id')
            ->whereIn('company_user.company_id', $company_id)
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
        ini_set('memory_limit', '2048M'); // revise this

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
        $company_ids = Auth::user()->companies->pluck(['id']); //used to filter with same company

        $request->validate([
            'user_id' => ['required', new AllowedScheduler()],
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
            if($schedule_type == '1' || $schedule_type == '5' || $schedule_type == '7'){

                $request->validate([
                    'customer_codes' => 'required',
                    'radius' => 'numeric|max:0.5',
                ]);

                $customer_codes = $request->customer_codes;

                foreach ($customer_codes as $customer_code){
                    $customer = Customer::where('customer_code', $customer_code)
                        ->whereIn('company_id', $company_ids)
                        ->first();

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
                    'address' => ['required'],
                ]);

                $place = get_google_map_place($request->address)['place'];
                $coordinates = get_google_map_coordinates($request->address)['coordinates'];

                $schedule = new Schedule();
                $schedule->user_id = $request->user_id;
                $schedule->type = $schedule_type;
                $schedule->code = $schedule_code;
                $schedule->name = $request->name;
                $schedule->address = $place;
                $schedule->date = $date;
                $schedule->start_time = $request->start_time;
                $schedule->end_time = $request->end_time;
                $schedule->status = '2';
                $schedule->remarks = $request->remarks;
                $schedule->lat = $coordinates['lat'];
                $schedule->lng = $coordinates['lng'];
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
            'user_id' => ['required', new AllowedScheduler()],
            'date' => 'required',
            'radius' => 'required',
            'start_time' => [new TimeRule($request->start_time, $request->end_time), 'required'],
            'end_time' => 'required',
        ]);

        $schedule_type = $request->type;

        #Customer & Office Visit
        if($schedule_type == '1' || $schedule_type == '5'){
            $request->validate([
                'customer_code' => [new GeocodeCustomerRule(), 'required'],
                'radius' => 'numeric|max:0.5',
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
                'address' => ['required'],
            ]);

            $place = get_google_map_place($request->address)['place'];
            $coordinates = get_google_map_coordinates($request->address)['coordinates'];

            $schedule = Schedule::find($id);
            $schedule->user_id = $request->user_id;
            $schedule->type = $request->type;
            $schedule->code = Schedule::createScheduleCode($schedule_type);
            $schedule->name = $request->name;
            $schedule->address = $place;
            $schedule->start_time = $request->start_time;
            $schedule->end_time = $request->end_time;
            $schedule->status = '2';
            $schedule->remarks = $request->remarks;
            $schedule->lat = $coordinates['lat'];
            $schedule->lng = $coordinates['lng'];
            $schedule->km_distance = $request->radius;
            $schedule->save();
        }

        $data = $this->dataOutput($request->date,$request->date,$request->user_id,$schedule->code);
        return response()->json($data);
    }

    public function change(Request $request, $id){
        $schedule = Schedule::find($id);

        Validator::make([$schedule->user_id], [new AllowedScheduler()])->validate();

        $schedule->date = $request->date;
        $schedule->save();
        return $schedule;
    }

    public function destroy($id)
    {
        $schedule = Schedule::find($id);

        Validator::make([$schedule->user_id], [new AllowedScheduler()])->validate();

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
            ->whereHas('user', function ($q){
               $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
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

    function scheduleCustomerData(Request $request, $classification){

        $searchKey = $request->q;

        $company_ids = Auth::user()->companies->pluck('id'); //filter by auth company


        $customers = Customer::select(DB::raw("CONCAT(name, ' - ', street) AS name"), 'customer_code')
            ->when($classification != 'null', function ($q) use($classification){
                $q->where('classification', $classification);
            })
            ->whereIn('company_id', $company_ids)
            ->where(DB::raw("CONCAT(name, ' - ', street)"), 'LIKE', "%$searchKey%")
            ->get([
                'name',
                'customer_code'
            ])->take(10);


        return $customers;

    }


    public function missedItineraries(){
        return view('schedule.missed-itineraries');
    }

    public function missedItinerariesData(Request $request){

        $request->validate([
            // 'company' => 'required',
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);
        
        if($request->company){
            $company_id = $request->company;
        }else{
            $company_id = Auth::user()->companies->first()->id;
        }
        
        $users = User::select('id')->where('company_id',$company_id)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        $missed_itineraries= Schedule::with('attendances','user')
                        ->doesnthave('attendances')
                        ->whereDate('created_at', '>=',  $request->startDate)
                        ->whereDate('created_at' ,'<=', $request->endDate)
                        ->whereIn('user_id',$selected_user)
                        ->orderBy('id','desc')->get();

        return $missed_itineraries;
    }

    public function virtualScheduleReport(){
        session(['header_text' => 'Virtual Schedule Report']);
        return view('schedule.virtual_schedule_report');
    }

    public function virtualScheduleReportDataToday(){
        $dateToday = date('Y-m-d');
        $default_company_id = Auth::user()->companies->first()->id;
        $users = User::select('id')->where('company_id',$default_company_id)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }
        return Schedule::with('attendances','user','schedule_type')->where('date',$dateToday)->whereIn('user_id',$selected_user)->where('type','7')->get();
    }

    public function virtualScheduleReportDataFilter(Request $request){
        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);
        
        if($request->company){
            $company_id = $request->company;
        }else{
            $company_id = Auth::user()->companies->first()->id;
        }
        
        $users = User::select('id')->where('company_id',$company_id)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        return Schedule::with('attendances','user','schedule_type')
                            ->whereDate('date', '>=',  $request->startDate)
                            ->whereDate('date' ,'<=', $request->endDate)
                            ->whereIn('user_id',$selected_user)
                            ->where('type','7')
                            ->get();
    }

    public function changePlannedSchedules(){
        return view('schedule.change-planned-schedules');
    }

    public function changePlannedSchedulesData(Request $request){

        session(['header_text' => 'Schedules']);


        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);

        $data = $request->all();
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $companyId = $data['company'] ? $data['company'] : Auth::user()->companies[0]->id;

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        return Audit::with('schedule','schedule.user')->where('auditable_type','App\Schedule')
                        ->where('old_values','like','%date%')
                        ->where('event','=','updated')
                        ->whereHas('schedule',function ($q) use($selected_user){
                            $q->whereIn('user_id',$selected_user);
                        })
                        ->where('created_at','>=',$data['startDate'])
                        ->where('created_at','<=',$data['endDate'])
                        ->orderBy('auditable_id','ASC')
                        ->get();
    }
}
