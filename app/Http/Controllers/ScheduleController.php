<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Rules\TimeRule;
use App\Schedule;
use App\ScheduleTypes;
use App\TechnicalSalesRepresentative;
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

        $schedules = DB::select('CALL p_schedules(\'%%\', \'%%\', \'%%\')');

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

        return view('schedule.index', compact(
            'schedules',
            'tsrs',
            'scheduleTypes',
            'customers'
        ));
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

            $customer_codes = $request->customer_codes;

            DB::beginTransaction();
            foreach ($customer_codes as $customer_code){
                $customer = Customer::where('customer_code', $customer_code)->first();

                $schedule = new Schedule();
                $schedule->user_id = $request->user_id;
                $schedule->type = $request->type;
                $schedule->code = $customer_code;
                $schedule->name = $customer->name;
                $schedule->address = $customer->town_city;
                $schedule->date = $request->date;
                $schedule->start_time = $request->start_time;
                $schedule->end_time = $request->end_time;
                $schedule->status = '2';
                $schedule->remarks = $request->remarks;
                $schedule->save();
            }
            DB::commit();

            foreach ($customer_codes as $customer_code){
                $data[] = collect(DB::select('CALL p_schedules(\''. $request->date .'\', \'' . $request->user_id . '\', \'' . $customer_code . '\')'))->first();
            }
        }
        #Event & Mapping
        else{
            $schedule = new Schedule();
            $schedule->user_id = $request->user_id;
            $schedule->type = $request->type;
            $schedule->code = '';
            $schedule->name = $request->name;
            $schedule->address = $request->address;
            $schedule->date = $request->date;
            $schedule->start_time = $request->start_time;
            $schedule->end_time = $request->end_time;
            $schedule->status = '2';
            $schedule->remarks = $request->remarks;
            $schedule->save();

            $data[] = collect(DB::select('CALL p_schedules(\''. $request->date .'\', \'' . $request->user_id . '\', \'' . $schedule->code . '\')'))->first();
        }

        return response()->json($data);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'tsr_id' => 'required',
            'customer_code' => 'required',
            'date' => 'required',
            'start_time' => [new TimeRule($request->start_time, $request->end_time), 'required'],
            'end_time' => 'required',
        ]);

        $schedule = Schedule::find($id);
        $schedule->customer_code = $request->customer_code;
        $schedule->start_time = $request->start_time;
        $schedule->end_time = $request->end_time;
        $schedule->status = '2';
        $schedule->remarks = $request->remarks;
        $schedule->save();

        $data = collect(DB::select('CALL p_schedules(\''. $request->date .'\', \'' . $request->tsr_id. '\', \'' . $request->customer_code . '\')'))->first();
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
}
