<?php

namespace App\Http\Controllers;

use Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use App\{
    Message,
    Payment
};

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session(['header_text' => 'Payment']);
        
        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('payment.index', compact('notification'));
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
        
        // $request->validate([
        //     'expenseId' => 'required',
        //     'userId' => 'required'
        // ]);
        $client = new Client([
            'base_uri' => 'http://10.96.4.39/salesforcepaymentservice/api/',
            'cookies' => true,
            ]);
        
        try {
            $response =  $client->request('POST', 'sap_payment_posting', [
                'form_params' => [
                    'posting_type' => 'CHECK',
                    'app_server' => '172.17.1.34',
                    'system_id' => 'FQA',
                    'instance_number' => '00',
                    'sap_user_id' => 'PAYPROJECT',
                    'sap_password' => 'welcome69+',
                    'sap_dev_name' => 'PFMC QAS',
                    'client' => '778',
                    'header_text' => 'Reimbursement',
                    'company_code' => 'PFMC',
                    'document_date' => '10-10-2018', 
                    'posting_date' => '10-10-2018',
                    'document_type' => 'KR',
                    'reference_number' => 'sample1',
                    'baseline_date' => '10-10-2018',
                    'vendor_code' => '0920000128',
                    'payment_terms' => '',
                    'gl_account_i7' => '0010180003',
                    'gl_account_i3' => '0010180001',
                    
                    'acc_item_no' => '1',
                    'acc_item_text' => 'Reimbursement',
                    'acc_gl_account' => '0020100010',
                    'acc_gl_description' => 'Accounts Payable',
                    'acc_assignment' => '0920000128',
                    'acc_input_tax_code' => '~',
                    'acc_internal_order' => '~',
                    'acc_cost_center' => '~',
                    'acc_amount' => '-1100',
                    'acc_charge_type' => '~',
                    'acc_business_area' => 'FL10',
                    'acc_or_number' => '~',
                    'acc_supplier_name' => '~',
                    'acc_address' => '~',
                    'acc_tin_number' => '~',

                    'acc_item_no' => '2',
                    'acc_item_text' => 'Food of Sir Ale',
                    'acc_gl_account' =>  '0060070001',
                    'acc_gl_description' => 'Representation & Entertainment',
                    'acc_assignment' => '11SRP02',
                    'acc_input_tax_code' => 'IX',
                    'acc_internal_order' => '~',
                    'acc_cost_center' => '1110140200',
                    'acc_amount' => '1100',
                    'acc_charge_type' => 'A01', 
                    'acc_business_area' => 'FL10',
                    'acc_or_number' => 'resibo1',
                    'acc_supplier_name' => 'carendderia ni mang tomas',
                    'acc_address' => 'taghirap city',
                    'acc_tin_number' => '12123123',
                    
                    // 'acc_item_no' => '3',
                    // 'acc_item_text' => 'Food of Maam Donna',
                    // 'acc_gl_account' => '0060070001',
                    // 'acc_gl_description' => 'Representation & Entertainment',
                    // 'acc_assignment' => '11SRP02',
                    // 'acc_input_tax_code' => 'I3',
                    // 'acc_internal_order' => '~',
                    // 'acc_cost_center' =>'1110140200',
                    // 'acc_amount' =>'67.86',
                    // 'acc_charge_type' => 'A01',
                    // 'acc_business_area' => 'FL10',
                    // 'acc_or_number' => 'resibo2',
                    // 'acc_supplier_names' =>'carendderia ni mang kepweng',
                    // 'acc_address' => 'tagyaman city',
                    // 'acc_tin_number' => '12123123',

                    // 'acc_item_no' => '4',
                    // 'acc_item_text' => 'Food of Marco',
                    // 'acc_gl_account' => '0060070001',
                    // 'acc_gl_description' => 'Representation & Entertainment',
                    // 'acc_assignment' => '11SRP02',
                    // 'acc_input_tax_code' => 'I7',
                    // 'acc_internal_order' => '~',
                    // 'acc_cost_center' =>'1110140200',
                    // 'acc_amount' => '267.86',
                    // 'acc_charge_type' => 'A01',
                    // 'acc_business_area' => 'FL10',
                    // 'acc_or_number' => 'resibo3',
                    // 'acc_supplier_name' => 'carendderia ni mang mj cruz',
                    // 'acc_address' => 'navotas baha city',
                    // 'acc_tin_number' => '12123123',
                    
                    // 'acc_item_no' => '5',
                    // 'acc_item_text' => 'Food of TERRENCE',
                    // 'acc_gl_account' => '0060070001',
                    // 'acc_gl_description' =>'Representation & Entertainment',
                    // 'acc_assignment' => '11SRP02',
                    // 'acc_input_tax_code' =>'IX',
                    // 'acc_internal_order' => '~',
                    // 'acc_cost_center' =>'1110140200',
                    // 'acc_amount' =>'500',
                    // 'acc_charge_type' =>'A01',
                    // 'acc_business_area' => 'FL10',
                    // 'acc_or_number' =>'~', 
                    // 'acc_supplier_name' => '~',
                    // 'acc_address' =>'~',
                    // 'acc_tin_number' => '~',
                    
                    // 'acc_item_no' => '6',
                    // 'acc_item_text' =>'~',
                    // 'acc_gl_account' =>'0010180001',
                    // 'acc_gl_description' => 'Input Tax - Services',
                    // 'acc_assignment' =>'~',
                    // 'acc_input_tax_code' =>'I3',
                    // 'acc_internal_order' =>'~',
                    // 'acc_cost_center' =>'~',
                    // 'acc_amount' =>'32.14',
                    // 'acc_charge_type' => '~',
                    // 'acc_business_area' =>'~',
                    // 'acc_or_number' =>'~',
                    // 'acc_supplier_name' =>  '~',
                    // 'acc_address' => '~',
                    // 'acc_tin_number' => '~',

                    // 'acc_item_no' => '7',
                    // 'acc_item_text' => '~',
                    // 'acc_gl_account' => '0010180003',
                    // 'acc_gl_description' => 'Input Tax - Goods',
                    // 'acc_assignment' => '~',
                    // 'acc_input_tax_code' => 'I7',
                    // 'acc_internal_order' => '~',
                    // 'acc_cost_center' => '~',
                    // 'acc_amount' => '32.14',
                    // 'acc_charge_type' => '~',
                    // 'acc_business_area' => '~',
                    // 'acc_or_number' => '~',
                    // 'acc_supplier_name:' => '~',
                    // 'acc_address' => '~',
                    // 'acc_tin_number' => '~'
                ]
            ]);
            $jsonBody = (string) $response->getBody();
            dd($jsonBody);
        }catch(BadResponseException $ex){
            $response = $ex->getResponse(); 
            $jsonBody = (string) $response->getBody();
        }

        // $ids = [];
        // foreach($request->expenseId as $expense){
        //     $ids[] = [
        //         'expense_id' => $expense,
        //         'user_id' => $request->userId,
        //         'created_at' => date('Y-m-d H:i:s'),
        //         'updated_at' => date('Y-m-d H:i:s')
        //     ];
        // }
        // if(Payment::insert($ids)){
        //     return 'true';
        // }
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
