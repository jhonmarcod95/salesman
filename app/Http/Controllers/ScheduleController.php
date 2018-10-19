<?php

namespace App\Http\Controllers;

use Auth;   
use App\Customer;
use App\Rules\TimeRule;
use App\Schedule;
use App\ScheduleTypes;
use App\TechnicalSalesRepresentative;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $tsrs = TechnicalSalesRepresentative::select(
            DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'user_id')
            ->pluck(
                'name',
                'user_id'
            );

        $customers = Customer::select(DB::raw("CONCAT(name,' - ',town_city) AS name"), 'customer_code')
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
            'schedules',
            'tsrs',
            'scheduleTypes',
            'customers',
            'notification'
        ));
    }

    public function indexData(Request $request, $date_from, $date_to){
        //search specific
        if($request->exists('tsrs')){
            $user_ids = $request->tsrs;
        }
        //search all
        else{
            $user_ids = TechnicalSalesRepresentative::all()
                ->pluck('user_id')
                ->toArray();
        }
        $user_ids = implode(",",$user_ids);


        $schedules = DB::select("CALL p_schedules ('$date_from','$date_to','$user_ids','%%')");
        return $schedules;
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'type' => 'required',
            'date' => 'required',
            'start_time' => [new TimeRule($request->start_time, $request->end_time), 'required'],
            'end_time' => 'required',
        ]);

        $schedule_type = $request->type;

        #Customer Visit
        if($schedule_type == '1'){

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
                $schedule->address = $customer->town_city;
                $schedule->date = $request->date;
                $schedule->start_time = $request->start_time;
                $schedule->end_time = $request->end_time;
                $schedule->status = '2';
                $schedule->remarks = $request->remarks;
                $schedule->save();

                $data[] = $this->dataOutput($request->date,$request->date,$request->user_id,$schedule->code);
            }
        }
        #Event & Mapping
        else{
            $request->validate([
                'name' => 'required|max:191',
                'address' => 'required|max:191',
            ]);

            $schedule = new Schedule();
            $schedule->user_id = $request->user_id;
            $schedule->type = $schedule_type;
            $schedule->code = Schedule::createScheduleCode($schedule_type);
            $schedule->name = $request->name;
            $schedule->address = $request->address;
            $schedule->date = $request->date;
            $schedule->start_time = $request->start_time;
            $schedule->end_time = $request->end_time;
            $schedule->status = '2';
            $schedule->remarks = $request->remarks;
            $schedule->save();

            $data[] = $this->dataOutput($request->date,$request->date,$request->user_id,$schedule->code);
        }

        return response()->json($data);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required',
            'date' => 'required',
            'start_time' => [new TimeRule($request->start_time, $request->end_time), 'required'],
            'end_time' => 'required',
        ]);

        $schedule_type = $request->type;

        #Customer Visit
        if($schedule_type == '1'){
            $request->validate([
                'customer_code' => 'required',
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
            $schedule->save();
        }
        #Event & Mapping
        else{
            $request->validate([
                'name' => 'required|max:191',
                'address' => 'required|max:191',
            ]);

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
}
