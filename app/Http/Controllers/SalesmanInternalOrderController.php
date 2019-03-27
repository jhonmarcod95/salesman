<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{
    SalesmanInternalOrder
};

class SalesmanInternalOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session(['header_text' => 'Internal Order']);

        return view('internal-order.index');
    }

    /**
     * Fetch all internal orders
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData()
    {
        return SalesmanInternalOrder::with('user')->orderBy('id', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'charge_type' => 'required', 
            'internal_order' => 'required',
            'sap_server' => 'required'
        ]);
        
        if($internal_order = SalesmanInternalOrder::create($request->all())){
            
            return SalesmanInternalOrder::with('user')->where('id', $internal_order->id)->first();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalesmanInternalOrder $salesmanInternalOrder)
    {
        if($salesmanInternalOrder->update($request->all())){

            return SalesmanInternalOrder::with('user')->where('id', $salesmanInternalOrder->id)->first();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalesmanInternalOrder $salesmanInternalOrder)
    {
        if($salesmanInternalOrder->delete()){
            return $salesmanInternalOrder;
        }
    }
}
