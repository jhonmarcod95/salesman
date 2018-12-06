<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequesetScheduleResource as RequesetScheduleResource;
use App\Http\Resources\CustomerResource as CustomerResource;
use Illuminate\Http\Request;
use App\RequestSchedule;
use App\Rules\TimeRule;
use Carbon\Carbon;
use App\Schedule;
use App\Customer;
use Auth;
use DB;

class RequestsAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = RequestSchedule::where('user_id', Auth::user()->id)
                            ->orderBy('id','DESC')
                            ->take(25)
                            ->get();

        return  RequesetScheduleResource::collection($request);
    }

    public function customers()
    {
        $customers = Customer::select('name',
                                'street',
                                'town_city',
                                'customer_code')->get();

        return CustomerResource::collection($customers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'date' => 'required',
            'start_time' => [new TimeRule($request->start_time, $request->end_time), 'required'],
            'end_time' => 'required'
        ]);

        // If customer was selected
        if($request->input('type') == '1') {
            $this->validate($request, [
                'customer_codes' => 'required',
            ]);
            //Find Customer
            $customer = Customer::where('customer_code', $request->customer_codes)->first();
        }
        // Mapping and Event Condition
        else {
            $this->validate($request, [
                'name' => 'required|max:191',
                'address' => 'required|max:191',
            ]);
        }

            // Store to Request Model
            $requestSchedule = new RequestSchedule();
            $requestSchedule->user_id = Auth::user()->id;
            $requestSchedule->type = $request->input('type');
            $requestSchedule->code = $request->type == '1' ?  $request->input('customer_codes') : Schedule::createScheduleCode($request->input('type'));
            $requestSchedule->name = $request->type == '1' ? $customer->name : $request->input('name');
            $requestSchedule->address = $request->type == '1' ? $customer->town_city : $request->input('address');
            $requestSchedule->date = $request->input('date');
            $requestSchedule->start_time = $request->input('start_time');
            $requestSchedule->end_time = $request->input('end_time');
            $requestSchedule->status = '2';
            $requestSchedule->remarks = $request->input('remarks');
            $requestSchedule->save();

            return new RequesetScheduleResource(RequestSchedule::find($requestSchedule->id));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
