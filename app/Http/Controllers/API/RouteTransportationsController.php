<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransportationResource;
use App\RouteTransportation;
use App\Transportation;
use Illuminate\Support\Facades\Auth;
use App\Expense;

class RouteTransportationsController extends Controller
{

    public function transportations()
    {
        $transportations = Transportation::all();
        return TransportationResource::collection($transportations);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            'expense_id' => 'required',
            'transportation_id' => 'required',
            'from' => 'required',
            'to' => 'required',
            // 'fare' => 'required',
        ]);

        // find if route has a image before adding recipt details
        $fare_expense = Expense::where('id', $request->expense_id)->first();
        if($fare_expense->attachment == "attachments/default.jpg") {
            $this->validate($request,[
                'receipt_image' => 'required'
            ],[
                'receipt_image.required' => "Please upload fare receipt photo first."
            ]);
        }

        // $routeTransportation =  = Auth::user()->routeTransportations()->create($request->all());
        $routeTransportation = new RouteTransportation;
        $routeTransportation->user_id = Auth::user()->id;
        $routeTransportation->from = $request->input('from');
        $routeTransportation->to = $request->input('to');
        // $routeTransportation->fare = $request->input('fare');
        $routeTransportation->remarks = $request->input('remarks');
        $routeTransportation->expense()->associate($request->input('expense_id'));
        $routeTransportation->transportation()->associate($request->input('transportation_id'));
        $routeTransportation->save();

        return $routeTransportation;

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RouteTransportation $routeTransportation)
    {
        $this->validate($request,[
            'from' => 'required',
            'to' => 'required',
            // 'fare' => 'required'
        ]);

        $routeTransportation->update($request->all());

        return $routeTransportation;
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
