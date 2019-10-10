<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use DB;
use Carbon\Carbon;
use App\{
    User,
    SapUser,
    Company,
    Expense,
    ExpensesEntry,
    Payment,
    PaymentHeader,
    PaymentHeaderError
};

class PaymentAutoPosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:autoposting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expenses post succesfully';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $companies = Company::where('hasSAP',1)->orderBy('id', 'desc')->get();
        $lastWeekMonday = date("Y-m-d", strtotime("last week monday"));
        $lastWeekSunday = date("Y-m-d", strtotime("last sunday"));
        $coveredWeek = Carbon::parse($lastWeekMonday)->format('m/d/Y') . ' to ' .Carbon::parse($lastWeekSunday)->format('m/d/Y');

        foreach($companies as $company){
            $expenses = Expense::doesntHave('payments')->with('user', 'user.companies', 'user.location','user.vendor', 'user.internalOrders', 'user.companies.businessArea', 'user.companies.glTaxcode','expensesType','expensesType.expenseChargeType.chargeType.expenseGl', 'receiptExpenses','receiptExpenses.receiptType')
            ->whereHas('user' , function($q) use($company){
                $q->whereHas('companies', function ($q) use($company){
                    $q->where('company_id', $company->id);
                });
            })->whereDate('created_at', '>=',  $lastWeekMonday)
            ->whereDate('created_at' ,'<=', $lastWeekSunday)
            ->where('expenses_entry_id', '!=', 0)
            ->get()
            ->groupBy('user.id');
            $this->simulateExpense($expenses,$coveredWeek, $lastWeekMonday, $lastWeekSunday);
        }
    }

    public function simulateExpense($expenses ,$coveredWeek, $lastWeekMonday, $lastWeekSunday){

        foreach($expenses as  $expense){
            //Group entry by month
            $groupedArrayExpenses = [];
            $baseline_date = '';
            foreach($expense as $key => $e){
                $month = date('n', strtotime($e->created_at));
                //Group entries by month
                if (!isset($groupedArrayExpenses[$month])) {
                    $groupedArrayExpenses[$month][0] = $e;
                }else{
                    $groupedArrayExpenses[$month][$key] = $e;
                }
                // Get baseline date
                if (!$baseline_date) { // set date first to  $baseline_date
                    $baseline_date = $e->created_at;
                }else{
                    if($baseline_date->greaterThan($e->created_at)){ // compare $baseline_date to current created_at to get most recent date
                        $baseline_date = $e->created_at;
                    }
                }
            }
            $posting_date = '';
            $sameMonth = date("n",  strtotime($lastWeekMonday)) == date("n", strtotime($lastWeekSunday));
            //Simulate entry
            foreach($groupedArrayExpenses as $groupedExpenses){ // Loop month's. if there's an entry with different month
               
                if(!$sameMonth){ // Set posting date for different month
                    $firstMonth = date("n",  strtotime($lastWeekMonday));
                    $posting_date = $firstMonth == date('n', strtotime($groupedExpenses[0]->created_at)) ? $groupedExpenses[0]->created_at->endOfMonth() : Carbon::now();
                }else{// Same month but check first if cover week is same month in auto posting date run 
                    $samePostingDate = date("n",  strtotime($lastWeekSunday)) == date("n", strtotime(Carbon::now()));
                    $posting_date = !$samePostingDate ? Carbon::parse($lastWeekSunday)->endOfMonth() : Carbon::now();
                }

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
                // $acc_tax_amountI3 = [];
                // $acc_tax_amountI7 = [];
                $gl_account_i7 = '';
                $gl_account_i3 = '';
                $expense_ids = [];
                
                $filteredBusinessArea = $groupedExpenses[0]->user->companies[0]->businessArea
                    ->where('location_id',$groupedExpenses[0]->user->location[0]->id)->first(); //Loop to get the correct business area base on the and user's location

                $item = 1;
                $tax_amount = 0;
                $tax_amountI3 = 0;
                $tax_amountI7 = 0;
                $acc_amount_first_index = 0;

                // Generate the line 1
                array_push($acc_item_no, 1);
                array_push($acc_item_text, 'SALESFORCE REIMBURSEMENT; '. $coveredWeek);
                array_push($acc_gl_account, $groupedExpenses[0]->user->vendor->vendor_code);
                array_push($acc_gl_description, $groupedExpenses[0]->user->name);
                array_push($acc_assignment, '~');
                array_push($acc_input_tax_code, '~');
                array_push($acc_internal_order, '~');
                array_push($acc_charge_type, '~');
                array_push($acc_business_area, $filteredBusinessArea->business_area ? $filteredBusinessArea->business_area : '~');
                array_push($acc_or_number, '~');
                array_push($acc_supplier_name, '~');
                array_push($acc_address, '~');
                array_push($acc_tin_number, '~');
                // array_push($acc_tax_amountI3, '~');
                // array_push($acc_tax_amountI7, '~');

                foreach($groupedExpenses as $expense){ // Loop entry per month. This will generate line 2 to the nth line
                    array_push($expense_ids,$expense->id);
                    $filteredGL = $expense->expensesType->expenseChargeType->chargeType->expenseGl->where('charge_type', $expense->expensesType->expenseChargeType->chargeType->name)
                    ->where('company_code', $expense->user->companies[0]->code)->first(); //Loop to get correct GL base on the charge type and user company code
                    $filteredInternalOrders = $expense->user->internalOrders->where('charge_type',$expense->expensesType->expenseChargeType->chargeType->name)->first(); //Loop to get correct IO base on the charge type of expense
                    
                    // Hard coded  for special scenario for specific company
                    $amount = '';  
                    $tax_code = '';
                    $or_number = '';
                    $supplier_name = '';
                    $supplier_address = '';
                    $bol_tax_amount = false;

                    if($expense->user->companies[0]->code == '1100' || $expense->user->companies[0]->code == 'CSCI'){
                        $amount = $expense->amount;
                        $tax_code = 'IX';
                        $bol_tax_amount = false;
                    }else if($expense->user->companies[0]->code == 'PFMC' && substr($filteredBusinessArea->business_area, 0, 2) == 'FD'){
                        $amount = $expense->amount;
                        $tax_code = 'IX';
                    }else{
                        $tax_code = $expense->receiptExpenses ? $expense->receiptExpenses->receiptType->tax_code : 'IX';

                        if($tax_code  == 'IX'){
                            $amount = number_format($expense->amount, 2, '.', "");
                            $tax_amount = number_format($expense->amount, 2, '.', "");
                        }else{
                            $amount = number_format($expense->amount / 1.12, 2, '.', "");
                            $tax_amount = number_format($expense->amount - ($expense->amount / 1.12), 2, '.', "");
                            $bol_tax_amount = true;
                        }
                    }
                    $acc_amount_first_index = $acc_amount_first_index + $amount;

                    $internal_order = $filteredInternalOrders ? $filteredInternalOrders->internal_order : '~';

                    array_push($acc_item_no, $item = $item + 1);
                    array_push($acc_item_text, 'SALESFORCE ' . strtoupper($expense->expensesType->name. ' ' .$expense->created_at->format('m/d/Y')));
                    array_push($acc_gl_account,  $filteredGL->gl_account);
                    array_push($acc_gl_description, $filteredGL->gl_description);
                    array_push($acc_assignment, '~');
                    array_push($acc_input_tax_code, $tax_code);
                    array_push($acc_internal_order, $internal_order);
                    array_push($acc_amount, $amount);
                    array_push($acc_charge_type, $expense->expensesType->expenseChargeType->chargeType->name);
                    array_push($acc_business_area, $filteredBusinessArea->business_area ? $filteredBusinessArea->business_area : '~');
                    array_push($acc_or_number, $expense->receiptExpenses && $expense->receiptExpenses->receipt_number ? $expense->receiptExpenses->receipt_number : '~');
                    array_push($acc_supplier_name, $expense->receiptExpenses && $expense->receiptExpenses->vendor_name ? $expense->receiptExpenses->vendor_name : '~');
                    array_push($acc_address, $expense->receiptExpenses && $expense->receiptExpenses->vendor_address ? $expense->receiptExpenses->vendor_address : '~');
                    array_push($acc_tin_number, $expense->receiptExpenses && $expense->receiptExpenses->tin_number ? $expense->receiptExpenses->tin_number : '~');
                    // array_push($acc_tax_amountI3, '~');
                    // array_push($acc_tax_amountI7, '~');

                    if($bol_tax_amount && $expense->receiptExpenses->receiptType->tax_code == 'I3'){
                        $tax_amountI3 = number_format($tax_amountI3 + $tax_amount, 2, '.', "");
                    }elseif($bol_tax_amount && $expense->receiptExpenses->receiptType->tax_code == 'I7'){
                        $tax_amountI7 = number_format($tax_amountI7 + $tax_amount, 2, '.', "");
                    }else{}
                }

                // Generate last line of the entry for I7 or I3
                $gl_account_i7 = $groupedExpenses[0]->user->companies[0]->glTaxcode->where('tax_code', 'I7')->first();

                if($tax_amountI7){
                    array_push($acc_item_no, $item = $item + 1);
                    array_push($acc_item_text, '~');
                    array_push($acc_gl_account,  $gl_account_i7->gl_account);
                    array_push($acc_gl_description, $gl_account_i7->gl_description);
                    array_push($acc_assignment, '~');
                    array_push($acc_input_tax_code, 'I7');
                    array_push($acc_internal_order, '~');
                    array_push($acc_amount, $tax_amountI7);
                    array_push($acc_charge_type, '~');
                    array_push($acc_business_area, $filteredBusinessArea->business_area ? $filteredBusinessArea->business_area : '~');
                    array_push($acc_or_number, '~');
                    array_push($acc_supplier_name, '~');
                    array_push($acc_address, '~');
                    array_push($acc_tin_number, '~');
                    $acc_amount_first_index = $acc_amount_first_index + $tax_amountI7;
                }

                $gl_account_i3 = $groupedExpenses[0]->user->companies[0]->glTaxcode->where('tax_code', 'I3')->first();
                if($tax_amountI3){
                    array_push($acc_item_no, $item = $item + 1);
                    array_push($acc_item_text, '~');
                    array_push($acc_gl_account,  $gl_account_i3->gl_account);
                    array_push($acc_gl_description, $gl_account_i3->gl_description);
                    array_push($acc_assignment, '~');
                    array_push($acc_input_tax_code, 'I3');
                    array_push($acc_internal_order, '~');
                    array_push($acc_amount, $tax_amountI3);
                    array_push($acc_charge_type, '~');
                    array_push($acc_business_area, $filteredBusinessArea->business_area ? $filteredBusinessArea->business_area : '~');
                    array_push($acc_or_number, '~');
                    array_push($acc_supplier_name, '~');
                    array_push($acc_address, '~');
                    array_push($acc_tin_number, '~');
                    $acc_amount_first_index = $acc_amount_first_index + $tax_amountI3;
                }

                //Place total amount to be reimburse in the first index of @acc_amount
                array_unshift($acc_amount, number_format($acc_amount_first_index * -1, 2, '.', "")); 
                // Get SAP server
                $sapCredential = $this->simulateExpenseSubmitted($groupedExpenses[0]->expenses_entry_id);
                // Post Simulated Expeses to SAP                
                $this->postSimulatedExpenses($acc_item_no,$acc_item_text,$acc_gl_account,$acc_gl_description,$acc_assignment,$acc_input_tax_code,$acc_internal_order,$acc_amount,$acc_charge_type,$acc_business_area,$acc_or_number,$acc_supplier_name,$acc_address,$acc_tin_number,$groupedExpenses[0]->user,$gl_account_i7, $gl_account_i3, $expense_ids, $sapCredential, $posting_date,$baseline_date);


            }
        }
    }

    public function postSimulatedExpenses($acc_item_no,$acc_item_text,$acc_gl_account,$acc_gl_description,$acc_assignment,$acc_input_tax_code,$acc_internal_order,$acc_amount,$acc_charge_type,$acc_business_area,$acc_or_number,$acc_supplier_name,$acc_address,$acc_tin_number,$user,$gl_account_i7, $gl_account_i3, $expense_ids, $sapCredential, $posting_date,$baseline_date){
        $posting_type = 'POST';
        $array = [
            'posting_type' => $posting_type,
            'app_server' => $sapCredential[0]['sap_server']->app_server,
            'system_id' =>  $sapCredential[0]['sap_server']->system_id,
            'instance_number' => $sapCredential[0]['sap_server']->system_number,
            'sap_user_id' => $sapCredential[0]['sap_user']->sap_id,
            'sap_password' => urlencode($sapCredential[0]['sap_user']->sap_password),
            'sap_name' => $sapCredential[0]['sap_server']->name,
            'client' => $sapCredential[0]['sap_server']->client,
            'header_text' => 'REIMBURSEMENT',
            'company_code' => $user->companies[0]->code,
            'document_date' => Carbon::now()->format('m/d/Y'),
            'posting_date' => $posting_date->format('m/d/Y'),
            'document_type' => 'KR',
            'reference_number' => $sapCredential[0]['reference_number'],
            'baseline_date' => $baseline_date->format('m/d/Y'), 
            'vendor_code' => $user->vendor->vendor_code,
            'payment_terms' => 'NCOD',
            'gl_account_i7' => $gl_account_i7->gl_account,
            'gl_account_i3' => $gl_account_i3->gl_account,
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
        
        $params = http_build_query($array) . "\n";
        $decode_params = urldecode($params);
        $trimmed_params = preg_replace('/\[[^\]]*\]/' , '', $decode_params);

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

            if($sap_errors == 0 &&  $posting_type == 'POST'){ //Check if has error in SAP posting 
                DB::beginTransaction();
                try {
                    $ids = [];
                    foreach($expense_ids as $expense_id){
                        $ids[] = [
                            'expense_id' => $expense_id,
                            'user_id' => $user->id,
                            'document_code' => json_decode($response, true)[0]['return_message_description'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    if(Payment::insert($ids)){ // Save Posted expense to payment table
                        $array_header = [
                            'company_code' => $user->companies[0]->code,
                            'company_name' => $user->companies[0]->name,
                            'reference_number' => $sapCredential[0]['reference_number'],
                            'ap_user' => $sapCredential[0]['sap_user']->sap_id,
                            'vendor_code' => $user->vendor->vendor_code,
                            'vendor_name' =>  $user->name,
                            'document_type' => 'KR',
                            'payment_terms' => 'NCOD',
                            'header_text' => "REIMBURSEMENT",
                            'document_date' => Carbon::now()->format('Y-m-d'),
                            'posting_date' => $posting_date->format('Y-m-d'),
                            'baseline_date' => $baseline_date->format('Y-m-d'),
                            'document_code' => json_decode($response, true)[0]['return_message_description'],
                        ];
                        if($payment_header = PaymentHeader::create($array_header)){ //Save transaction header to payment header table
                            foreach($array['acc_item_no'] as $key => $item){
                                $array_details = [
                                    'item' => $item,
                                    'item_text' => $array['acc_item_text'][$key],
                                    'gl_account' => $array['acc_gl_account'][$key],
                                    'description' => $array['acc_gl_description'][$key],
                                    'assignment' => $array['acc_assignment'][$key],
                                    'input_tax_code' => $array['acc_input_tax_code'][$key],
                                    'internal_order' => $array['acc_internal_order'][$key],
                                    'amount' => $array['acc_amount'][$key],
                                    'charge_type' => $array['acc_charge_type'][$key],
                                    'business_area' => $array['acc_business_area'][$key],
                                    'or_number' => $array['acc_or_number'][$key],
                                    'supplier_name' => $array['acc_supplier_name'][$key],
                                    'supplier_address' => $array['acc_address'][$key],
                                    'supplier_tin_number' => $array['acc_tin_number'][$key],
                                ];
                                if($paymentDetail = $payment_header->paymentDetail()->create($array_details)){ //Save posted expenses per line to Payment Detail table
                                    if($array['acc_internal_order'][$key] !== '~'){

                                        preg_match('/\d{2}\/\d{2}\/\d{4}/',$array['acc_item_text'][$key],$match);

                                        $url = "http://10.96.4.39/salesforcepaymentservice/api/sap_budget_checking";
                                        $fields = "budget_line=". $array['acc_internal_order'][$key] ."&posting_date=". Carbon::parse($match[0])->format('Y-m-d') ."&company_server=".$sapCredential[0]['sap_server']->sap_server;
                                        $header = array(
                                            "Content-Type: application/x-www-form-urlencoded",
                                            "Postman-Token: bce74f1d-8de3-41e6-9384-5f8b39f75e71",
                                            "cache-control: no-cache"
                                        );

                                        $array_balance = [
                                            'internal_order' => $array['acc_internal_order'][$key],
                                            'date' => Carbon::parse($match[0])->format('Y-m-d'),
                                            'to' => json_decode($this->APIconnection($url,$fields,$header), true)[0]['balance_amount']
                                        ];

                                        $paymentDetail->balanceHistory()->create($array_balance);
                                    }
                                }
                            }
                            // return $response;
                        }

                        DB::commit();
                        // return $response;
                    }
                   
                } catch (Exception $e) {
                    DB::rollBack();
                }
            }

            // Save errors to the database
            if($sap_errors > 0){
                DB::beginTransaction();
                try {
                    $data = [
                        'user_id' => $user->id,
                        'ap_id' => 175,
                        'cover_week' => $array['acc_item_text'][0],
                        'posting_type' => $posting_type

                    ];
                    if($paymentHeaderError = PaymentHeaderError::create($data)){
                        $errors = json_decode($response);

                        foreach( $errors as $err){
                            if($err->return_message_type == 'E'){
                                $array_details = [
                                    'return_message_type' => $err->return_message_type,
                                    'return_message_id' => $err->return_message_id,
                                    'return_message_number' => $err->return_message_number,
                                    'return_message_description' => $err->return_message_description,
                                ];
                                $paymentHeaderError->paymentHeaderDetailError()->create($array_details);
                            }
                        }

                        DB::commit();
                        // return $response;
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                }
            }
            // return $response;
        }

    }

     /**
     * Simulate submitted expenses
     *
     * @return \Illuminate\Http\Response
     */
    public function simulateExpenseSubmitted($id){
        $expenseEntry = ExpensesEntry::findOrfail($id);

        if($expenseEntry){
            $tsr = User::findOrFail($expenseEntry->user_id);
            $sap_server = $tsr->companies[0]->sapServers[0];
            $sap_user = SapUser::where('user_id', 175)->where('sap_server', $sap_server->sap_server)->first();
            $header_query = PaymentHeader::where('ap_user', $sap_user->sap_id)->get()->last();
            $reference_number = '000000000';
            if($header_query){
                $reference_number = substr($header_query->reference_number, -9);
            }

            $data = [
                'sap_user' => $sap_user,
                'sap_server' => $sap_server,
                'reference_number' => 175 . $reference_number + 1
            ];

            return array ($data);
        }
    }

    /**
     * API connection 
     *
     * @return \Illuminate\Http\Response
     */
 
    public function APIconnection($url, $fields, $header){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

}
