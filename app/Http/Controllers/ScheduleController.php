<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Schedule;
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
            DB::raw("CONCAT(first_name,' ',last_name) AS name"),'id')
            ->pluck(
                'name',
                'id'
            );

        $customers = Customer::select(DB::raw("CONCAT(name,' - ',town_city) AS name"), 'customer_code')
            ->pluck(
                'name',
                'customer_code'
            );


        return view('schedule.index', compact(
            'schedules',
            'tsrs',
            'customers'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tsr_id' => 'required',
            'customer_codes' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $customer_codes = $request->customer_codes;

        DB::beginTransaction();
        foreach ($customer_codes as $customer_code){
            $schedule = new Schedule();
            $schedule->tsr_id = $request->tsr_id;
            $schedule->customer_code = $customer_code;
            $schedule->date = $request->date;
            $schedule->start_time = $request->start_time;
            $schedule->end_time = $request->end_time;
            $schedule->status = '2';
            $schedule->remarks = $request->remarks;
            $schedule->save();
        }
        DB::commit();

        foreach ($customer_codes as $customer_code){
            $data[] = collect(DB::select('CALL p_schedules(\''. $request->date .'\', \'' . $request->tsr_id . '\', \'' . $customer_code . '\')'))->first();
        }

        return response()->json($data);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'tsr_id' => 'required',
            'customer_code' => 'required',
            'date' => 'required',
            'start_time' => 'required',
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
