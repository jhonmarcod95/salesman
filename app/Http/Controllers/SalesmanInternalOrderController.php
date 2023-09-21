<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\ChargeTypeRule;
use App\{
    SalesmanInternalOrder,
    ExpenseRate,
    ChargeType
};
use Auth;
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
        return SalesmanInternalOrder::with('user', 'user.expenseRate', 'chargeType.expenseChargeType.expenseType')
            ->whereHas('user', function ($q){
                $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
            })
            ->orderBy('id', 'desc')->get();
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
            'charge_type' => ['required', new ChargeTypeRule($request->user_id)], 
            'internal_order' => 'required',
            'sap_server' => 'required',
            'amount' => 'required|integer'
        ]);

        if($internal_order = SalesmanInternalOrder::create($request->all())){

            $chargeType = ChargeType::with('expenseChargeType.expenseType')->where('name', $request->charge_type)->first();

            $array = [
                'created_by' => Auth::user()->id,
                'user_id' => $request->user_id,
                'expenses_type_id' => $chargeType->expenseChargeType->expenseType->id,
                'amount' => $request->amount,
            ];
            ExpenseRate::create($array);
            return SalesmanInternalOrder::with('user','user.expenseRate', 'chargeType.expenseChargeType.expenseType')->where('id', $internal_order->id)->first();
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
        $request->validate([
            'user_id' => 'required',
            'charge_type' => ['required', new ChargeTypeRule($request->user_id, $salesmanInternalOrder->id, 'Edit')], 
            'internal_order' => 'required',
            'sap_server' => 'required',
            'amount' => 'required|integer',
            'default_expense_type' => 'required'
        ]);

        if($salesmanInternalOrder->update($request->all())){

            $chargeType = ChargeType::with('expenseChargeType.expenseType')->where('name', $request->charge_type)->first();
            $expenseRate = ExpenseRate::where('user_id',$request->user_id)->where('expenses_type_id', $request->default_expense_type)->first();

            $array = [
                'created_by' => Auth::user()->id,
                'user_id' => $request->user_id,
                'expenses_type_id' => $chargeType->expenseChargeType->expenseType->id,
                'amount' => $request->amount,
            ];
            $expenseRate ? $expenseRate->update($array) : ExpenseRate::create($array);

            return SalesmanInternalOrder::with('user','user.expenseRate','chargeType.expenseChargeType.expenseType')->where('id', $salesmanInternalOrder->id)->first();
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
