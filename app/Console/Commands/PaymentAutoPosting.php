<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use DB;
use Carbon\Carbon;
use App\{Http\Controllers\APIController,
    User,
    SapUser,
    Company,
    Expense,
    ExpensesEntry,
    Payment,
    PaymentHeader,
    PaymentHeaderError};

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
        $companies = Company::where('hasSAP', 1)->orderBy('id', 'desc')->get();
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

                $expense_ids = [];
                $items = [];

                $filteredBusinessArea = $groupedExpenses[0]->user->companies[0]->businessArea
                    ->where('location_id',$groupedExpenses[0]->user->location[0]->id)->first(); //Loop to get the correct business area base on the and user's location

                $item = 1;
                $tax_amount = 0;
                $tax_amountI3 = 0;
                $tax_amountI7 = 0;
                $acc_amount_first_index = 0;

                // Generate the line 1
                array_push($items, [
                    'item_no' => 1,
                    'item_text' => 'SALESFORCE REIMBURSEMENT; '. $coveredWeek,
                    'gl_account' => $groupedExpenses[0]->user->vendor->vendor_code,
                    'gl_description' => $groupedExpenses[0]->user->name,
                    'assignment' => '',
                    'input_tax_code' => '',
                    'internal_order' => '',
                    'amount' => '',
                    'charge_type' => '',
                    'business_area' => $filteredBusinessArea->business_area ? $filteredBusinessArea->business_area : '',
                    'or_number' => '',
                    'supplier_name' => '',
                    'address' => '',
                    'tin_number' => '',
                    'tag' => 'ap'
                ]);

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

                    $internal_order = $filteredInternalOrders ? $filteredInternalOrders->internal_order : '';

                    array_push($items, [
                        'item_no' =>  $item = $item + 1,
                        'item_text' => 'SALESFORCE ' . strtoupper($expense->expensesType->name . ' ' . $expense->created_at->format('m/d/Y')),
                        'gl_account' => $filteredGL->gl_account,
                        'gl_description' => $filteredGL->gl_description,
                        'assignment' => '',
                        'input_tax_code' => $tax_code,
                        'internal_order' =>  $internal_order,
                        'amount' => $amount,
                        'charge_type' => $expense->expensesType->expenseChargeType->chargeType->name,
                        'business_area' => $filteredBusinessArea->business_area ? $filteredBusinessArea->business_area : '',
                        'or_number' => $expense->receiptExpenses && $expense->receiptExpenses->receipt_number ? $expense->receiptExpenses->receipt_number : '',
                        'supplier_name' => $expense->receiptExpenses && $expense->receiptExpenses->vendor_name ? $expense->receiptExpenses->vendor_name : '',
                        'address' => $expense->receiptExpenses && $expense->receiptExpenses->vendor_address ? $expense->receiptExpenses->vendor_address : '',
                        'tin_number' => $expense->receiptExpenses && $expense->receiptExpenses->tin_number ? $expense->receiptExpenses->tin_number : '',
                        'expense_date' => $expense->created_at->format('Y-m-d'),
                        'tag' => 'gl'
                    ]);

                    if($bol_tax_amount && $expense->receiptExpenses->receiptType->tax_code == 'I3'){
                        $tax_amountI3 = number_format($tax_amountI3 + $tax_amount, 2, '.', "");
                    }elseif($bol_tax_amount && $expense->receiptExpenses->receiptType->tax_code == 'I7'){
                        $tax_amountI7 = number_format($tax_amountI7 + $tax_amount, 2, '.', "");
                    }else{}
                }

                // Generate last line of the entry for I7 or I3
                $gl_account_i7 = $groupedExpenses[0]->user->companies[0]->glTaxcode->where('tax_code', 'I7')->first();

                if($tax_amountI7){
                    array_push($items, [
                        'item_no' =>  $item = $item + 1,
                        'item_text' => '',
                        'gl_account' => $gl_account_i7->gl_account,
                        'gl_description' => $gl_account_i7->gl_description,
                        'assignment' => '',
                        'input_tax_code' => 'I7',
                        'internal_order' => '',
                        'amount' => $tax_amountI7,
                        'charge_type' => '',
                        'business_area' => $filteredBusinessArea->business_area ? $filteredBusinessArea->business_area : '',
                        'or_number' => '',
                        'supplier_name' => '',
                        'address' => '',
                        'tin_number' => '',
                        'tag' => 'tax'
                    ]);
                    $acc_amount_first_index = $acc_amount_first_index + $tax_amountI7;
                }

                $gl_account_i3 = $groupedExpenses[0]->user->companies[0]->glTaxcode->where('tax_code', 'I3')->first();
                if($tax_amountI3){
                    array_push($items, [
                        'item_no' =>  $item = $item + 1,
                        'item_text' => '',
                        'gl_account' => $gl_account_i3->gl_account,
                        'gl_description' => $gl_account_i3->gl_description,
                        'assignment' => '',
                        'input_tax_code' => 'I3',
                        'internal_order' => '',
                        'amount' => $tax_amountI3,
                        'charge_type' => '',
                        'business_area' => $filteredBusinessArea->business_area ? $filteredBusinessArea->business_area : '',
                        'or_number' => '',
                        'supplier_name' => '',
                        'address' => '',
                        'tin_number' => '',
                        'tag' => 'tax'
                    ]);
                    $acc_amount_first_index = $acc_amount_first_index + $tax_amountI3;
                }

                //Place total amount to be reimburse in the first index of @acc_amount
                $items[0]['amount'] = number_format($acc_amount_first_index * -1, 2, '.', "");
                // Get SAP server
                $sapCredential = $this->simulateExpenseSubmitted($groupedExpenses[0]->expenses_entry_id);
                // Post Simulated Expeses to SAP
                $this->postSimulatedExpenses($items,$groupedExpenses[0]->user,$gl_account_i7, $gl_account_i3, $expense_ids, $sapCredential, $posting_date,$baseline_date, $lastWeekMonday, $lastWeekSunday);
            }
        }
    }

    public function postSimulatedExpenses($items,$user,$gl_account_i7, $gl_account_i3, $expense_ids, $sapCredential, $posting_date, $baseline_date, $lastWeekMonday, $lastWeekSunday){
        $posting_type = 'POST';
        $payment_terms = 'NCOD';
        $document_type = 'KR';
        $company_code = $user->companies[0]->code;
        $company_name = $user->companies[0]->name;

        $sapConnection = [
            'ashost' => $sapCredential[0]['sap_server']->app_server,
            'sysnr' => $sapCredential[0]['sap_server']->system_number,
            'client' => $sapCredential[0]['sap_server']->client,
            'user' => $sapCredential[0]['sap_user']->sap_id,
            'passwd' => $sapCredential[0]['sap_user']->sap_password,
            'BAPI_TRANSACTION_COMMIT'
        ];

        $accounting_entry = [
            'posting_type' => $posting_type,
            'header_text' => 'REIMBURSEMENT',
            'company_code' => $company_code,
            'document_date' => Carbon::now()->format('m/d/Y'),
            'posting_date' => $posting_date->format('m/d/Y'),
            'document_type' => $document_type,
            'reference_number' => $sapCredential[0]['reference_number'],
            'baseline_date' => $baseline_date->format('m/d/Y'),
            'vendor_code' => $user->vendor->vendor_code,
            'payment_terms' => $payment_terms,
            'gl_account_i7' => $gl_account_i7->gl_account,
            'gl_account_i3' => $gl_account_i3->gl_account,
            'items' => $items
        ];

        $items = $accounting_entry['items'];

        $last_io_balances = [];

        /* ref key logic **********************************************************************************************/
        $io_ref_keys = [];
        $ioTotalExpenses = collect($items)
            ->where('tag', 'gl')
            ->groupBy('internal_order')
            ->map(function ($row) {
                return $row->sum('amount');
            });

        foreach ($ioTotalExpenses as $io => $ioTotalExpense){
            $ioBalances = APIController::executeSapFunction($sapConnection, 'ZFI_BUDGET_CHK_INTEG', [
                'P_AUFNR' => $io,
                'P_BUDAT' => $posting_date->format('Ymd'),
            ], [
                'GV_OUTPUT' => 'total_amount',
                [
                    'TABLE' => ['VERSIONS' => 'versions'],
                    'FIELDS' => [
                        'VERSN' => 'version',
                        'AMOUNT' => 'amount',
                    ]
                ]
            ]);

            //for balance history table
            $last_io_balances[] = [
                'internal_order' => $io,
                'amount' => $ioBalances['total_amount']
            ];

            $ioBalanceVersions = $ioBalances['versions'];

            // IO has no version zero (*rare, problem in SAP)
            if (collect($ioBalanceVersions)->contains('version', '!=', '0')){ // has no version zero, will add version zero
                array_unshift($ioBalanceVersions, (object) ['version' => '000', 'amount' => '0']);
            }

            $versionCount = count($ioBalanceVersions) - 1; // (-1) not include version zero
            $keyCount = 3; // ref keys in SAP
            $refKeyCombinations = [];

            //key combination [[1,2,3],[2,3,4]]
            if ($keyCount > $versionCount) $keyCount = $versionCount;

            for ($i = 1; $i <= $versionCount; $i++){
                $keys = [];
                for ($j = 0; $j < $keyCount; $j++){
                    $keys['REF_KEY_' . ($j + 1)] = $i + $j;
                }
                $refKeyCombinations[] = $keys;
                if ($keys['REF_KEY_' . $keyCount] >= $versionCount) break;
            }

//            //key to be used
//            foreach ($refKeyCombinations as $refKeyCombination){
//                $ioTotalBalance = $ioBalances['versions'][0]->amount;
//                $keyToUsed = [];
//                $isSatisfied = false;
//
//                if ($ioTotalBalance > $ioTotalExpense) break; // if version zero is enough, no ref keys needed
//
//                foreach ($refKeyCombination as $k => $v){
//                    $ioTotalBalance += $ioBalances['versions'][$v]->amount;
//
//                    $keyToUsed[$k] = $v;
//                    if ($ioTotalBalance > $ioTotalExpense) {
//                        $isSatisfied = true;
//                        break;
//                    };
//                }
//
//                if ($isSatisfied){
//                    $io_ref_keys[] = array_merge([
//                        'internal_order' => $io,
//                    ], $keyToUsed);
//                }
//
//                $ioTotalBalance = 0; //set to reset for next combination
//            }

            //keys to be used
            foreach ($refKeyCombinations as $refKeyCombination){
                $ioTotalBalance = $ioBalanceVersions[0]->amount;

                foreach ($refKeyCombination as $k => $v){
                    $ioTotalBalance += $ioBalanceVersions[$v]->amount;
                }

                if ($ioTotalBalance > $ioTotalExpense){
                    $io_ref_keys[] = array_merge([
                        'internal_order' => $io,
                    ], $refKeyCombination);
                    break;
                }
            }

        }
        $io_ref_keys = collect($io_ref_keys);
        /* ************************************************************************************************************/

        /* SAP payment posting setup **********************************************************************************/
        $documentHeader = [
            'DOCUMENTHEADER' => [
                'USERNAME' => strtoupper($sapCredential[0]['sap_user']->sap_id),
                'HEADER_TXT' => $accounting_entry['header_text'],
                'COMP_CODE' => $accounting_entry['company_code'],
                'DOC_DATE' => Carbon::now()->format('Ymd'),
                'PSTNG_DATE' => $posting_date->format('Ymd'),
                'FISC_YEAR' => $posting_date->format('Y'),
                'FIS_PERIOD' => $posting_date->format('m'),
                'DOC_TYPE' => $accounting_entry['document_type'],
                'REF_DOC_NO' => $accounting_entry['reference_number'],
            ]
        ];

        $accountPayable = [];
        $accountGL = [];
        $currencyAmount = [];
        $accountTax = [];
        $suppliers = [];
        $postingErrors = [];

        $totalAmountI7 = 0;
        $totalAmountI3 = 0;

        foreach ($items as $item){
            // AP
            if ($item['tag'] == 'ap'){
                $values = [
                    'ITEMNO_ACC' => $item['item_no'],
                    'VENDOR_NO' => $item['gl_account'],
                    'COMP_CODE' => $company_code,
                    'BLINE_DATE' => $baseline_date->format('Ymd'),
                    'ALLOC_NMBR' => $item['assignment'],
                    'ITEM_TEXT' => $item['item_text'],
                    'PMNTTRMS' => $payment_terms,
                ];
                if ($company_code == 'PFMC') $values['BUS_AREA'] = $item['business_area'];
                $accountPayable[] = $values;

                $currencyAmount[] = [
                    'ITEMNO_ACC' => $item['item_no'],
                    'CURRENCY' => 'PHP',
                    'AMT_DOCCUR:double' => $item['amount'],
                ];
            }
            // GL
            elseif ($item['tag'] == 'gl'){

                $ref_keys = $io_ref_keys->where('internal_order', $item['internal_order'])->first();
                if (is_null($ref_keys)) $ref_keys = [];
                unset($ref_keys['internal_order']);

                $values = array_merge([
                    'ITEMNO_ACC' => $item['item_no'],
                    'GL_ACCOUNT' => $item['gl_account'],
                    'COMP_CODE' => $company_code,
                    'TAX_CODE' => $item['input_tax_code'],
                    'ORDERID' => $item['internal_order'],
                    'ITEM_TEXT' => $item['item_text'],
                    'ALLOC_NMBR' => $item['assignment'],
                ], $ref_keys);

                if ($company_code == 'PFMC') $values['BUS_AREA'] = $item['business_area'];
                if (($company_code == '1100' &&
                        ($item['gl_account'] == '0060010007' || $item['gl_account'] == '0070090010' || $item['gl_account'] == '0060010006')) ||
                    ($company_code == '1200' &&
                        ($item['gl_account'] == '0060010007' || $item['gl_account'] == '0070090010' || $item['gl_account'] == '0060010006')) ||
                    ($company_code == 'PFMC' &&
                        ($item['gl_account'] == '0060082001'))
                ){
                    $values['QUANTITY:int'] = '1';
                    $values['BASE_UOM'] = '10';
                }
                $accountGL[] = $values;

                $currencyAmount[] = [
                    'ITEMNO_ACC' => $item['item_no'],
                    'CURRENCY' => 'PHP',
                    'AMT_DOCCUR:double' => $item['amount'],
                ];

                // sum all i7 and i3 amount
                if ($item['input_tax_code'] == 'I7'){
                    $totalAmountI7 += $item['amount'];
                }
                elseif ($item['input_tax_code'] == 'I3'){
                    $totalAmountI3 += $item['amount'];
                }

                // for long text posting
                if ($item['input_tax_code'] != 'IX'){
                    $suppliers[] = [
                        'item_no' => $item['item_no'],
                        'name' => $item['supplier_name'],
                        'address' => $item['address'],
                        'tin' => $item['tin_number'],
                        'or_number' => $item['or_number']
                    ];
                }
            }
            // Tax
            elseif ($item['tag'] == 'tax'){
                $accountTax[] = [
                    'ITEMNO_ACC' => $item['item_no'],
                    'GL_ACCOUNT' => $item['gl_account'],
                    'TAX_CODE' => $item['input_tax_code'],
                    'TAX_RATE:int' => '12',
                    'ITEMNO_TAX' => '1',
                ];

                if ($item['input_tax_code'] == 'I7'){
                    $currencyAmount[] = [
                        'ITEMNO_ACC' => $item['item_no'],
                        'CURRENCY' => 'PHP',
                        'AMT_DOCCUR:double' => $item['amount'],
                        'AMT_BASE:double' => $totalAmountI7,
                    ];
                }
                elseif ($item['input_tax_code'] == 'I3'){
                    $currencyAmount[] = [
                        'ITEMNO_ACC' => $item['item_no'],
                        'CURRENCY' => 'PHP',
                        'AMT_DOCCUR:double' => $item['amount'],
                        'AMT_BASE:double' => $totalAmountI3,
                    ];
                }
            }
        }

        $accountPayable = ['ACCOUNTPAYABLE' => $accountPayable];
        $accountGL = ['ACCOUNTGL' => $accountGL];
        $currencyAmount = ['CURRENCYAMOUNT' => $currencyAmount];
        $accountTax = ['ACCOUNTTAX' => $accountTax];

        //final parameter for posting
        $payment = array_merge($documentHeader, $accountPayable, $accountGL, $currencyAmount, $accountTax);

        try{
            //sap posting
            $paymentPosting = APIController::executeSapFunction($sapConnection, 'BAPI_ACC_DOCUMENT_POST', $payment, null);
            $postingResults = $paymentPosting['RETURN'];

            foreach ($postingResults as $postingResult){
                //success posting
                if ($postingResult->TYPE == 'S'){
                    $document_code = substr($paymentPosting['OBJ_KEY'], 0, 10);

                    //store posted payments to DB
//                    DB::beginTransaction();
                    try {
                        $ids = [];
                        foreach($expense_ids as $expense_id){
                            $ids[] = [
                                'expense_id' => $expense_id,
                                'user_id' => $user->id,
                                'document_code' => $document_code,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ];
                        }
                        if(Payment::insert($ids)){ // Save Posted expense to payment table
                            if($payment_header = PaymentHeader::create([
                                'company_code' => $company_code,
                                'company_name' => $company_name,
                                'reference_number' => $accounting_entry['reference_number'],
                                'ap_user' => $sapConnection['user'],
                                'vendor_code' => $accounting_entry['vendor_code'],
                                'vendor_name' =>  $user->name,
                                'document_type' => $document_type,
                                'payment_terms' => $payment_terms,
                                'header_text' => $accounting_entry['header_text'],
                                'document_date' => Carbon::now()->format('Y-m-d'),
                                'posting_date' => $posting_date->format('Y-m-d'),
                                'baseline_date' => $baseline_date->format('Y-m-d'),
                                'document_code' => $document_code,
                                'expense_from' => $lastWeekMonday,
                                'expense_to' => $lastWeekSunday,
                                ])){ //Save transaction header to payment header table

                                foreach($items as $item){
                                    if($paymentDetail = $payment_header->paymentDetail()->create([
                                        'item' => $item['item_no'],
                                        'item_text' => $item['item_text'],
                                        'gl_account' => $item['gl_account'],
                                        'description' => $item['gl_description'],
                                        'assignment' => $item['assignment'],
                                        'input_tax_code' => $item['input_tax_code'],
                                        'internal_order' => $item['internal_order'],
                                        'amount' => $item['amount'],
                                        'charge_type' => $item['charge_type'],
                                        'business_area' => $item['business_area'],
                                        'or_number' => $item['or_number'],
                                        'supplier_name' => $item['supplier_name'],
                                        'supplier_address' => $item['address'],
                                        'supplier_tin_number' => $item['tin_number']])){ //Save posted expenses per line to Payment Detail table

                                        if ($item['tag'] == 'gl'){
                                            $last_io_balance = collect($last_io_balances)->where('internal_order', $item['internal_order'])->first()['amount'];

                                            $curr_io_balance = APIController::executeSapFunction($sapConnection, 'ZFI_BUDGET_CHK_INTEG', [
                                                'P_AUFNR' => $item['internal_order'],
                                                'P_BUDAT' => $posting_date->format('Ymd'),
                                            ], ['GV_OUTPUT' => 'total_amount'])['total_amount'];

                                            $array_balance = [
                                                'internal_order' => $item['internal_order'],
                                                'date' => $item['expense_date'],
                                                'from' => $last_io_balance,
                                                'to' => $curr_io_balance
                                            ];

                                            $paymentDetail->balanceHistory()->create($array_balance);
                                        }
                                    }
                                }
                            }
//                            DB::commit();
                        }
                    } catch (Exception $e) {
//                        DB::rollBack();
                    }

                    /* long text posting ******************************************************************************/
                    foreach ($suppliers as $supplier){
                        $textLines = [];
                        $tdname = $company_code . $document_code . $posting_date->format('Y') . str_pad($supplier['item_no'], '3', '0', STR_PAD_LEFT);
                        //note
                        $textLines[] = [
                            'MANDT' => $sapConnection['client'],
                            'TDOBJECT' => 'DOC_ITEM',
                            'TDNAME' => $tdname,
                            'TDID' => '001',
                            'TDSPRAS' => 'EN',
                            'COUNTER' => '001',
                            'TDFORMAT' => '*',
                            'TDLINE' => '',
                        ];
                        //supplier
                        $textLines[] = [
                            'MANDT' => $sapConnection['client'],
                            'TDOBJECT' => 'DOC_ITEM',
                            'TDNAME' => $tdname,
                            'TDID' => '002',
                            'TDSPRAS' => 'EN',
                            'COUNTER' => '002',
                            'TDFORMAT' => '*',
                            'TDLINE' => $supplier['name'],
                        ];
                        //address
                        $textLines[] = [
                            'MANDT' => $sapConnection['client'],
                            'TDOBJECT' => 'DOC_ITEM',
                            'TDNAME' => $tdname,
                            'TDID' => '003',
                            'TDSPRAS' => 'EN',
                            'COUNTER' => '003',
                            'TDFORMAT' => '*',
                            'TDLINE' => $supplier['address'],
                        ];
                        //tin
                        $textLines[] = [
                            'MANDT' => $sapConnection['client'],
                            'TDOBJECT' => 'DOC_ITEM',
                            'TDNAME' => $tdname,
                            'TDID' => '004',
                            'TDSPRAS' => 'EN',
                            'COUNTER' => '004',
                            'TDFORMAT' => '*',
                            'TDLINE' => $supplier['tin'],
                        ];
                        //or
                        $textLines[] = [
                            'MANDT' => $sapConnection['client'],
                            'TDOBJECT' => 'DOC_ITEM',
                            'TDNAME' => $tdname,
                            'TDID' => '005',
                            'TDSPRAS' => 'EN',
                            'COUNTER' => '005',
                            'TDFORMAT' => '*',
                            'TDLINE' => $supplier['or_number'],
                        ];

                        $textLines = ['TEXT_LINES' => $textLines];
                        APIController::executeSapFunction($sapConnection, 'RFC_SAVE_TEXT', $textLines, null);
                    }
                    /* ************************************************************************************************/
                    break;
                }
                //error posting
                elseif ($postingResult->TYPE == 'E'){
                    //collect posting errors
                    $postingErrors[] = [
                          'return_message_type' => $postingResult->TYPE,
                          'return_message_id' => $postingResult->ID,
                          'return_message_number' => $postingResult->NUMBER,
                          'return_message_description' => $postingResult->MESSAGE,
                    ];
                }
            }

            // store posting errors to the database
            if ($postingErrors){
                DB::beginTransaction();
                try {
                    if($paymentHeaderError = PaymentHeaderError::create([
                        'user_id' => $user->id,
                        'ap_id' => '175',
                        'cover_week' => $items[0]['item_text'],
                        'posting_type' => $posting_type])){

                        foreach( $postingErrors as $postingError){
                            if ($postingError['return_message_id'] != 'RW')
                            $paymentHeaderError->paymentHeaderDetailError()->create($postingError);
                        }
                        DB::commit();
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                }
            }
        }
        catch (\Exception $e){
            dd('#Error: ' . $e);
            //write file here
        }
        return;
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
