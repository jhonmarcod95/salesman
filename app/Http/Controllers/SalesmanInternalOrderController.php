<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\ChargeTypeRule;
use App\{
    SalesmanInternalOrder,
    ExpenseRate,
    ChargeType,
    Company,
    GlAccount,
    SapServer,
    SapUser
};
use Auth;
use Carbon\Carbon;

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
        //get sap server
        $sap_server = optional(Company::with('sapServers')->where('id', $request->company_id)->first())->sapServers->pluck('sap_server')->first();
        //get uom and gl_account via SAP
        $request_sap = $this->fetchFromSap($sap_server, $request->internal_order);
        if (!$request_sap['status']) //budget line errors
            {
                return response()->json(['errors' => [
                    'internal_order' => [$request_sap['message']]]
                ], 422);
            }
        $request_sap = $request_sap['message'];
        $request->merge([
            'sap_server' => $sap_server,
            'uom' => $request_sap['uom'],
            'gl_account_id' => $request_sap['gl_account_id'],
        ]);
        $request->validate([
            'user_id' => 'required',
            'charge_type' => ['required', new ChargeTypeRule($request->user_id)], 
            'internal_order' => 'required',
            'sap_server' => 'required',
            'uom' => 'required',
            'gl_account_id' => 'required',
            'amount' => 'nullable|numeric|min:1'
        ]);

        if($internal_order = SalesmanInternalOrder::create($request->all())){

            $chargeType = ChargeType::with('expenseChargeType.expenseType')->where('name', $request->charge_type)->first();
            if($request->filled('amount')){
                $array = [
                    'created_by' => Auth::user()->id,
                    'user_id' => $request->user_id,
                    'expenses_type_id' => $chargeType->expenseChargeType->expenseType->id,
                    'amount' => $request->amount,
                    'validity_date' => $request->validity_date,
                ];
                ExpenseRate::create($array);
            }
            return SalesmanInternalOrder::with('user','user.expenseRate', 'chargeType.expenseChargeType.expenseType')->where('id', $internal_order->id)->first();
        }
    }

    /**
     * Fetch uom & gl_account from SAP
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchFromSap($sap_server, $internal_order)
    {
        $sap_server = SapServer::where('sap_server', $sap_server)->first();
        $sap_user = SapUser::where('user_id', 175)->where('sap_server', $sap_server->sap_server)->first();
        $sap_connection = [
            'ashost' => $sap_server->app_server,
            'sysnr' => $sap_server->system_number,
            'client' => $sap_server->client,
            'user' => $sap_user->sap_id,
            'passwd' => $sap_user->sap_password
        ];
        $io_detail = APIController::executeSapFunction($sap_connection, 'ZFM_SUPBUD_INTEG', [
            'I_AUFNR' => $internal_order,
            'I_PERIOD' => Carbon::now()->format('m'),
            'I_YEAR' => Carbon::now()->format('Y'),
        ], null,$sap_server->sap_server);
            $return = [
                'message'=> '',
                'status'=> ''
            ];
            if ($io_detail['O_AUFNR']){
                $gl_account = str_pad($io_detail['O_GLACCOUNT'], '10', '0', STR_PAD_LEFT);
                $gl_account_id = GlAccount::where('code', $gl_account)->pluck('id')->first();
                if ($io_detail['O_UNIT']){
                    $uom = APIController::executeSapFunction($sap_connection,'ZCONVERSION_EXIT_CUNIT_INPUT',[
                        'INPUT' => $io_detail['O_UNIT']
                    ],null,$sap_server->sap_server);
                } else {
                    $return['message'] = 'Budget line does not have UOM.';
                    $return['status'] = false;
                    return $return;
                }
                
                $request_sap['uom'] = $uom ? $uom['OUTPUT'] : $io_detail['O_UNIT'];
                $request_sap['gl_account_id'] = $gl_account_id;
                $return['message'] = $request_sap;
                $return['status'] = true;
                return $return;
                // return $request_sap;
            } 
            else {
                // Return an error
                $return['message'] = 'Budget line does not exist.';
                $return['status'] = false;
                return $return;
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
            'amount' => 'nullable|integer|min:1',
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
                'validity_date' => $request->validity_date,
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
