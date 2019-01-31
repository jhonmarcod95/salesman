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
      $acc_item_no = [];
      $acc_item_text = [];
      $acc_gl_account = [];
      $acc_gl_description = [];
      $acc_assignment = [];
      $acc_input_tax_code = [];
      $acc_internal_order = [];
      $acc_amount = [];
      $acc_charge_type = [];
      $acc_business_area = [];
      $acc_or_number = [];
      $acc_supplier_name =[];
      $acc_address = [];
      $acc_tin_number = [];

      foreach(array ($request->expenseEntryId)  as $expense){
        array_push($acc_item_no,"1","2");
        array_push($acc_item_text,"Reimbursement","Food of Devy");
        array_push($acc_gl_account,"0000000957","0060070001");
        array_push($acc_gl_description,"TERRENCE TEJADA","Representation & Entertainment");
        array_push($acc_assignment,"0920000128","11SRP02");
        array_push($acc_input_tax_code,"","IX");
        array_push($acc_internal_order,"","P600603S0418");
        array_push($acc_amount,"-1100","1100");
        array_push($acc_charge_type,"","A01");
        array_push($acc_business_area,"FL10","FL10");
        array_push($acc_or_number,"","resibo1");
        array_push($acc_supplier_name,"","valenzuela city");
        array_push($acc_address,"","valenzuela city");
        array_push($acc_tin_number,"","12123123");

      }
      $array = [
          'posting_type' => $request->posting_type,
          'app_server' => $request->app_server,
          'system_id' =>  $request->system_id,
          'instance_number' => $request->instance_number,
          'sap_user_id' => $request->sap_user_id,
          'sap_password' => $request->sap_password,
          'sap_name' => $request->sap_name,
          'client' => $request->client,
          'header_text' => $request->header_text,
          'company_code' => $request->company_code,
          'document_date' => $request->document_date,
          'posting_date' => $request->posting_date,
          'document_type' => $request->document_type,
          'reference_number' => $request->reference_number,
          'baseline_date' => $request->baseline_date, 
          'vendor_code' => $request->vendor_code,
          'payment_terms' => $request->payment_terms,
          'gl_account_i7' => $request->gl_account_i7,
          'gl_account_i3' => $request->gl_account_i3,
          'acc_item_no' => $acc_item_no,
          'acc_item_text' => $acc_item_text,
          'acc_gl_account' => $acc_gl_account,
          'acc_gl_description' => $acc_gl_description,
          'acc_assignment' => $acc_assignment,
          'acc_input_tax_code' => $acc_input_tax_code,
          'acc_internal_order' => $acc_internal_order,
          'acc_amount' => $acc_amount,
          'acc_charge_type' => $acc_charge_type,
          'acc_business_area' => $acc_business_area,
          'acc_or_number' => $acc_or_number,
          'acc_supplier_name' => $acc_supplier_name,
          'acc_address' => $acc_address,
          'acc_tin_number' => $acc_tin_number
      ];

    $params = http_build_query($array);
    $cleanedParams = preg_replace('/%5B(\d+?)%5D/', '', $params);

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => "http://10.96.4.39/salesforcepaymentservice/api/sap_payment_posting",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $cleanedParams,
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/x-www-form-urlencoded",
        "Postman-Token: bce74f1d-8de3-41e6-9384-5f8b39f75e71",
        "cache-control: no-cache"
    ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        dd("cURL Error #:" . $err);
    } else {
        return $response;
    }
     

        
        // $request->validate([
        //     'expenseId' => 'required',
        //     'userId' => 'required'
        // ]);

        // $client = new Client([
        //     'base_uri' => 'http://10.96.4.39/salesforcepaymentservice/api/',
        //     'cookies' => true,
        //     ]);
        
        // try {
            
        //     $response =  $client->request('POST', 'sap_payment_posting', [
        //         'form_params' => [
                    // 'posting_type' => 'CHECK',
                    // 'app_server' => '172.17.1.34',
                    // 'system_id' => 'FQA',
                    // 'instance_number' => '00',
                    // 'sap_user_id' => 'PAYPROJECT',
                    // 'sap_password' => 'welcome69+',
                    // 'sap_dev_name' => 'PFMC QAS',
                    // 'client' => '778',
                    // 'header_text' => 'Reimbursement',
                    // 'company_code' => 'PFMC',
                    // 'document_date' => '10-10-2018', 
                    // 'posting_date' => '10-10-2018',
                    // 'document_type' => 'KR',
                    // 'reference_number' => 'sample1',
                    // 'baseline_date' => '10-10-2018',
                    // 'vendor_code' => '0920000128',
                    // 'payment_terms' => '',
                    // 'gl_account_i7' => '0010180003',
                    // 'gl_account_i3' => '0010180001',
                    
                    // 'acc_item_no' => '1',
                    // 'acc_item_text' => 'Reimbursement',
                    // 'acc_gl_account' => '0020100010',
                    // 'acc_gl_description' => 'Accounts Payable',
                    // 'acc_assignment' => '0920000128',
                    // 'acc_input_tax_code' => '',
                    // 'acc_internal_order' => '',
                    // 'acc_cost_center' => '',
                    // 'acc_amount' => '-1100',
                    // 'acc_charge_type' => '',
                    // 'acc_business_area' => 'FL10',
                    // 'acc_or_number' => '',
                    // 'acc_supplier_name' => '',
                    // 'acc_address' => '',
                    // 'acc_tin_number' => '',

                    // 'acc_item_no' => '2',
                    // 'acc_item_text' => 'Food of Sir Ale',
                    // 'acc_gl_account' =>  '0060070001',
                    // 'acc_gl_description' => 'Representation & Entertainment',
                    // 'acc_assignment' => '11SRP02',
                    // 'acc_input_tax_code' => 'IX',
                    // 'acc_internal_order' => '',
                    // 'acc_cost_center' => '1110140200',
                    // 'acc_amount' => '1100',
                    // 'acc_charge_type' => 'A01', 
                    // 'acc_business_area' => 'FL10',
                    // 'acc_or_number' => 'resibo1',
                    // 'acc_supplier_name' => 'carendderia ni mang tomas',
                    // 'acc_address' => 'taghirap city',
                    // 'acc_tin_number' => '12123123',
                    
                    // // 'acc_item_no' => '3',
                    // // 'acc_item_text' => 'Food of Maam Donna',
                    // // 'acc_gl_account' => '0060070001',
                    // // 'acc_gl_description' => 'Representation & Entertainment',
                    // // 'acc_assignment' => '11SRP02',
                    // // 'acc_input_tax_code' => 'I3',
                    // // 'acc_internal_order' => '',
                    // // 'acc_cost_center' =>'1110140200',
                    // // 'acc_amount' =>'67.86',
                    // // 'acc_charge_type' => 'A01',
                    // // 'acc_business_area' => 'FL10',
                    // // 'acc_or_number' => 'resibo2',
                    // // 'acc_supplier_names' =>'carendderia ni mang kepweng',
                    // // 'acc_address' => 'tagyaman city',
                    // // 'acc_tin_number' => '12123123',

                    // // 'acc_item_no' => '4',
                    // // 'acc_item_text' => 'Food of Marco',
                    // // 'acc_gl_account' => '0060070001',
                    // // 'acc_gl_description' => 'Representation & Entertainment',
                    // // 'acc_assignment' => '11SRP02',
                    // // 'acc_input_tax_code' => 'I7',
                    // // 'acc_internal_order' => '',
                    // // 'acc_cost_center' =>'1110140200',
                    // // 'acc_amount' => '267.86',
                    // // 'acc_charge_type' => 'A01',
                    // // 'acc_business_area' => 'FL10',
                    // // 'acc_or_number' => 'resibo3',
                    // // 'acc_supplier_name' => 'carendderia ni mang mj cruz',
                    // // 'acc_address' => 'navotas baha city',
                    // // 'acc_tin_number' => '12123123',
                    
                    // // 'acc_item_no' => '5',
                    // // 'acc_item_text' => 'Food of TERRENCE',
                    // // 'acc_gl_account' => '0060070001',
                    // // 'acc_gl_description' =>'Representation & Entertainment',
                    // // 'acc_assignment' => '11SRP02',
                    // // 'acc_input_tax_code' =>'IX',
                    // // 'acc_internal_order' => '',
                    // // 'acc_cost_center' =>'1110140200',
                    // // 'acc_amount' =>'500',
                    // // 'acc_charge_type' =>'A01',
                    // // 'acc_business_area' => 'FL10',
                    // // 'acc_or_number' =>'', 
                    // // 'acc_supplier_name' => '',
                    // // 'acc_address' =>'',
                    // // 'acc_tin_number' => '',
                    
                    // // 'acc_item_no' => '6',
                    // // 'acc_item_text' =>'',
                    // // 'acc_gl_account' =>'0010180001',
                    // // 'acc_gl_description' => 'Input Tax - Services',
                    // // 'acc_assignment' =>'',
                    // // 'acc_input_tax_code' =>'I3',
                    // // 'acc_internal_order' =>'',
                    // // 'acc_cost_center' =>'',
                    // // 'acc_amount' =>'32.14',
                    // // 'acc_charge_type' => '',
                    // // 'acc_business_area' =>'',
                    // // 'acc_or_number' =>'',
                    // // 'acc_supplier_name' =>  '',
                    // // 'acc_address' => '',
                    // // 'acc_tin_number' => '',

                    // // 'acc_item_no' => '7',
                    // // 'acc_item_text' => '',
                    // // 'acc_gl_account' => '0010180003',
                    // // 'acc_gl_description' => 'Input Tax - Goods',
                    // // 'acc_assignment' => '',
                    // // 'acc_input_tax_code' => 'I7',
                    // // 'acc_internal_order' => '',
                    // // 'acc_cost_center' => '',
                    // // 'acc_amount' => '32.14',
                    // // 'acc_charge_type' => '',
                    // // 'acc_business_area' => '',
                    // // 'acc_or_number' => '',
                    // // 'acc_supplier_name:' => '',
                    // // 'acc_address' => '',
                    // // 'acc_tin_number' => ''
        //         ]
        //     ]);
        //     $jsonBody = (string) $response->getBody();
        //     dd($jsonBody);
        // }catch(BadResponseException $ex){
        //     $response = $ex->getResponse(); 
        //     $jsonBody = (string) $response->getBody();
        // }

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
     * Show Expense Submitted page
     *
     * @return \Illuminate\Http\Response
     */
    public function showExpenseSubmitted($id){
        $expenseEntry = ExpensesEntry::findOrfail($id);

        if($expenseEntry){
            return view("expense.index-submitted", ['id' => $id]);
        }
    }

    public function simulateExpenseSubmitted($id){
        $expenseEntry = ExpensesEntry::findOrfail($id);

        if($expenseEntry){
            $tsr = User::findOrFail($expenseEntry->user_id);
            $tsr_company_code = $tsr->companies->pluck('code');
            $sap_user = SapUser::where('user_id', Auth::user()->id)->where('sap_server', $tsr_company_code)->first();
            $sap_server = SapServer::where('sap_server', $tsr_company_code)->first();
            
            $data = [
                'sap_user' => $sap_user,
                'sap_server' => $sap_server
            ];

            return array ($data);
        }
    }
}
