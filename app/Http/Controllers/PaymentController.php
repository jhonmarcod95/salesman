<?php

namespace App\Http\Controllers;

use Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use App\{
    Message,
    Payment,
    PaymentHeader,
    PaymentDetail
};
use DateTime;
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
        $acc_tax_amountI3 = [];
        $acc_tax_amountI7 = [];

        foreach($request->simulatedExpenses as $expense){

            $expense['item'] ? array_push($acc_item_no, $expense['item']) : array_push($acc_item_no, '~');
            $expense['item_text'] ? array_push($acc_item_text, $expense['item_text']) : array_push($acc_item_text, '~');
            $expense['gl_account'] ?  array_push($acc_gl_account, $expense['gl_account']) : array_push($acc_gl_account, '~');
            $expense['description'] ? array_push($acc_gl_description, $expense['description']) : array_push($acc_gl_description, '~');
            $expense['assignment'] ? array_push($acc_assignment, $expense['assignment']) : array_push($acc_assignment, '~');
            $expense['input_tax_code'] ? array_push($acc_input_tax_code, $expense['input_tax_code']) : array_push($acc_input_tax_code, '~');
            $expense['internal_order'] ? array_push($acc_internal_order, $expense['internal_order']) : array_push($acc_internal_order,'~');
            $expense['amount'] ? array_push($acc_amount, $expense['amount']) : array_push($acc_amount, '~');
            $expense['charge_type'] ? array_push($acc_charge_type, $expense['charge_type']) : array_push($acc_charge_type, '~');
            $expense['business_area'] ? array_push($acc_business_area, $expense['business_area']) : array_push($acc_business_area, '~');
            $expense['or_number'] ? array_push($acc_or_number, $expense['or_number']) :  array_push($acc_or_number, '~');
            $expense['supplier_name'] ? array_push($acc_supplier_name, $expense['supplier_name']) : array_push($acc_supplier_name, '~');
            $expense['supplier_address'] ? array_push($acc_address, $expense['supplier_address']) : array_push($acc_address, '~');
            $expense['supplier_tin_number'] ?  array_push($acc_tin_number, $expense['supplier_tin_number']) : array_push($acc_tin_number, '~');

            if (array_key_exists('tax_amountI3', $expense)) {
                array_push($acc_tax_amountI3, $expense['tax_amountI3']);
            }else if (array_key_exists('tax_amountI7', $expense)){
                array_push($acc_tax_amountI7, $expense['tax_amountI7']);
            }else{}
        }

        $array = [
            'posting_type' => $request->posting_type,
            'app_server' => $request->app_server,
            'system_id' =>  $request->system_id,
            'instance_number' => $request->instance_number,
            'sap_user_id' => $request->sap_user_id,
            'sap_password' => urlencode($request->sap_password),
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
    
        if($acc_tax_amountI3){
            $array['tax_amountI3'] = $acc_tax_amountI3;
        }
        if($acc_tax_amountI7){
            $array['tax_amountI7'] = $acc_tax_amountI7;
        }

        $params = http_build_query($array) . "\n";
        $decode_params = urldecode($params);
        $trimmed_params = preg_replace('/[\[{\(].*[\]}\)]/U' , '', $decode_params);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://10.96.4.39/salesforcepaymentservice/api/sap_payment_posting",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $trimmed_params,
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
            $sap_errors = 0;
            foreach(json_decode($response, true) as $r){
                if($r['return_message_type'] == 'E'){
                    $sap_errors =  $sap_errors + 1;
                }
            }

            if($request->posting_type == 'POST' && $sap_errors == 0){
                $request->validate([
                    'expenseId' => 'required',
                    'userId' => 'required'
                ]);
        
                $ids = [];
                foreach($request->expenseId as $expense){
                    $ids[] = [
                        'expense_id' => $expense,
                        'user_id' => $request->userId,
                        'document_code' => json_decode($response, true)[0]['return_message_description'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                }
                $document_date = new DateTime($request->document_date);
                $posting_date = new DateTime($request->posting_date);
                $baseline_date = new DateTime($request->baseline_date);
                if(Payment::insert($ids)){
                    $array_header = [
                        'company_code' => $request->company_code,
                        'company_name' => $request->company_name,
                        'reference_number' => $request->reference_number,
                        'ap_user' => $request->sap_user_id,
                        'vendor_code' => $request->vendor_code,
                        'vendor_name' =>  $request->vendor_name,
                        'document_type' => $request->document_type,
                        'payment_terms' => $request->payment_terms,
                        'header_text' => $request->header_text,
                        'document_date' => $document_date->format('Y-m-d'),
                        'posting_date' => $posting_date->format('Y-m-d'),
                        'baseline_date' => $baseline_date->format('Y-m-d'),
                        'document_code' => json_decode($response, true)[0]['return_message_description'],
                    ];
                    if($payment_header = PaymentHeader::create($array_header)){
                        foreach($request->simulatedExpenses as $expense){
                            $array_details = [
                                'item' => $expense['item'],
                                'item_text' => $expense['item_text'],
                                'gl_account' => $expense['gl_account'],
                                'description' => $expense['description'],
                                'assignment' => $expense['assignment'],
                                'input_tax_code' => $expense['input_tax_code'],
                                'internal_order' => $expense['internal_order'],
                                'amount' => $expense['amount'],
                                'charge_type' => $expense['charge_type'],
                                'business_area' => $expense['business_area'],
                                'or_number' => $expense['or_number'],
                                'supplier_name' => $expense['supplier_name'],
                                'supplier_address' => $expense['supplier_address'],
                                'supplier_tin_number' => $expense['supplier_tin_number'],
                            ];
                            $payment_header->paymentDetail()->create($array_details);
                        }

                        return $response;
                    }
                    return $response;
                }
            }
            return $response;
        }
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
