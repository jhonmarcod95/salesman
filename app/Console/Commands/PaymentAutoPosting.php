<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use DB;
use Carbon\Carbon;
use App\{CronLog,
    Http\Controllers\APIController,
    RunningCommand,
    User,
    SapUser,
    Company,
    Expense,
    ExpensesEntry,
    Payment,
    PaymentHeader,
    PaymentHeaderError,
    ExpenseMonthlyDmsReceive,
    ExpenseDeduction,
    EmployeeMonthlyExpense
};
use Illuminate\Support\Facades\Storage;

class PaymentAutoPosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:autoposting {sap_server}';

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
        CronLog::create(['name' => $this->signature]);
//        RunningCommand::create(['name' => $this->signature]); // this will force error if command runs at the same time

        $lastWeekMonday = date("Y-m-d", strtotime("last week monday"));
        $lastWeekSunday = date("Y-m-d", strtotime("last sunday"));
        $sap_server = $this->argument('sap_server');

        $this->generateExpense($lastWeekMonday, $lastWeekSunday, $sap_server);

//        RunningCommand::where('name', $this->signature)->delete();
    }

    public function generateExpense($dateFrom, $dateTo, $sap_server, $isReprocessing = false){

        $companies = Company::whereHas('sapServers', function ($q) use ($sap_server) {
                $q->where('sap_server', $sap_server);
            })
            ->where('hasSAP', 1)
            ->orderBy('id', 'desc')
            ->get();

        $coveredWeek = Carbon::parse($dateFrom)->format('m/d/Y') . ' to ' .Carbon::parse($dateTo)->format('m/d/Y');

        foreach($companies as $company){
            $expenses = Expense::doesntHave('payments')->with('user', 'user.companies', 'user.location','user.vendor', 'user.internalOrders', 'user.companies.businessArea', 'user.companies.glTaxcode','expensesType','expensesType.expenseChargeType.chargeType.expenseGl', 'receiptExpenses','receiptExpenses.receiptType')
                ->whereHas('user' , function($q) use($company){
                    $q->whereHas('companies', function ($q) use($company){
                        $q->where('company_id', $company->id);
                    });
                })->whereDate('created_at', '>=',  $dateFrom)
                ->whereDate('created_at' ,'<=', $dateTo)
                ->where('expenses_entry_id', '!=', 0)
                ->when(!$isReprocessing, function($q){
                    $q->where('status_id',4);//Not posted
                })
                ->when($isReprocessing, function($q){
                    $q->where('status_id',4);//For reprocessing 
                })
                ->get()
                ->groupBy('user.id');
                
            $this->simulateExpense($expenses,$coveredWeek, $dateFrom, $dateTo);
        }
    }

    public function simulateExpense($expenses ,$coveredWeek, $lastWeekMonday, $lastWeekSunday){
        foreach($expenses as $expense){
            //Group entry by month
            $groupedArrayExpenses = [];
            $baseline_date = '';
            foreach($expense as $key => $e){
                $month = date('n', strtotime($e->created_at));
                // Check if user has pending amount for deduction
                // if($this->checkPendingAmountForDeduction($e)){
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
                // }
            }
            $posting_date = '';
            $sameMonth = date("n",  strtotime($lastWeekMonday)) == date("n", strtotime($lastWeekSunday));
            //Simulate entry
            foreach($groupedArrayExpenses as $index => $groupedExpenses){ // Loop month's. if there's an entry with different month
                if(!$sameMonth){ // Set posting date for different month
                    $firstMonth = date("n",  strtotime($lastWeekMonday));
                    $posting_date = $firstMonth == date('n', strtotime($groupedExpenses[0]->created_at)) ? $groupedExpenses[0]->created_at->endOfMonth() : Carbon::now();
                }else{// Same month but check first if cover week is same month in auto posting date run
                    $samePostingDate = date("n",  strtotime($lastWeekSunday)) == date("n", strtotime(Carbon::now()));
                    $posting_date = !$samePostingDate ? Carbon::parse($lastWeekSunday)->endOfMonth() : Carbon::now();
                }
                // Checking of submitted receipts from previous months(Should be completed)
                $is_complete = $this->checkSubmittedReceipts($groupedExpenses[0]->user->id,$posting_date);
                if($is_complete){
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
                        'uom' => '',
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
                        if($expense = $this->checkPendingAmountForDeduction($expense,$posting_date)){
                            $company_code = $expense->user->companies[0]->code;

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
    
                            if($company_code == '1100' || $company_code == 'CSCI'){ // NON-VAT
                                $amount = $expense->amount;
                                $tax_code = 'IX';
                                $bol_tax_amount = false;
                            }else if($company_code == '2100' && substr($filteredBusinessArea->business_area, 0, 2) == 'FD'){ // NON-VAT
                                $amount = $expense->amount;
                                $tax_code = 'IX';
                            }else{
                                $tax_code = $expense->receiptExpenses ? $expense->receiptExpenses->receiptType->tax_code : 'IX';
    
                                if($tax_code  == 'IX'){ // NON-VAT
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
                            $uom = $filteredInternalOrders ? $filteredInternalOrders->uom : '';
    
                            // populate gl account
                            $gl_account_code = $filteredInternalOrders->gl_account->code ?? null; // gl from IO master
                            $gl_account_name = $filteredInternalOrders->gl_account->name ?? null;
    
                            array_push($items, [
                                'item_no' =>  $item = $item + 1,
                                'item_text' => 'SALESFORCE ' . strtoupper($expense->expensesType->name . ' ' . $expense->created_at->format('m/d/Y')),
                                'gl_account' =>  $gl_account_code,
                                'gl_description' => $gl_account_name,
                                'assignment' => '',
                                'input_tax_code' => $tax_code,
                                'internal_order' =>  $internal_order,
                                'uom' => $uom,
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
                    }

                    if(count($items) > 1){
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
                                'uom' => '',
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
                                'uom' => '',
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
                }else{
                    $this->recordForReposting($index,$groupedExpenses,$lastWeekMonday,$lastWeekSunday,$posting_date);
                }
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
        $sap_server = $sapCredential[0]['sap_user']->sap_server;

        if ($sap_server == 'HANA') $document_type = 'VG';


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

            //keys to be used
            foreach ($refKeyCombinations as $refKeyCombination){
                $ioTotalBalance = $ioBalanceVersions[0]->amount;

                foreach ($refKeyCombination as $k => $v){
                    $ioTotalBalance += $ioBalanceVersions[$v]->amount;
                }

                if ($ioTotalBalance >= $ioTotalExpense){
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
                // if ($company_code == '2100') $values['BUSINESSPLACE'] = 'AP10'; // todo:: should be table
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
                    'QUANTITY:int' => '1',
                    'BASE_UOM' => $item['uom'],
                ], $ref_keys);

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
                    'ITEMNO_TAX' => ($sap_server == 'HANA') ?  '0' : '1', // for HANA value is 0, for r3 is 1
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
        Storage::prepend('posting-entries-' . Carbon::now()->format('Y-m-d') . '.log', Carbon::now() . ' : ' . json_encode($payment));

//        dd($payment);

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
                                //Update status to posted
                                Expense::whereIn('id',$expense_ids)
                                    ->update([
                                        'status_id' => 3
                                    ]);
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
            RunningCommand::where('name', $this->signature)->delete();
            CronLog::create(['name' => $e]);
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
                'reference_number' => (175 . $reference_number) + 1
                // 'reference_number' => 175 . $reference_number + 1
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

    /**
     *  Check submitted receipts for previous months if complete
     *
     */
    public function checkSubmittedReceipts($user_id,$posting_date){
        $month = Carbon::parse($posting_date)->format('F');
        $year = Carbon::parse($posting_date)->format('Y');
        $start_month = 1;
        $end_month = intval(Carbon::parse($posting_date)->format('m'));
        $previous_month = ($end_month - 1); 
        
        if($month == 'January'){
            $year = $year - 1;
            $previous_month = 12;
        }
        //For 2024, start checking from month of August only
        if($year == '2024') $start_month = 8;
        //Generate months that has submitted expense
        $months = $this->generateMonths($start_month,$previous_month,$year,$user_id);

        // Convert month names to their respective digits
        // $month_digits = array_map(function ($month_name) {
        //     return Carbon::parse($month_name)->month;
        // }, $months);

        $result = false;
        // if(!Expense::where('user_id',$user_id)
        //     ->whereYear('created_at', $year)
        //     ->whereIn(DB::raw('MONTH(created_at)'), $month_digits)
        //     ->where('expenses_entry_id','!=',0)
        //     ->whereIn('verified_status_id',[0,2,3])
        //     ->first()){ //All months receipt must be fully

            $dms_submitteds = ExpenseMonthlyDmsReceive::where('user_id',$user_id)
                ->whereIn('month',$months)
                ->where('year',$year)
                ->get();

            // Complete receipt
            if($dms_submitteds->count() == count($months)) $result = true;
            // Exemption checking for 1 month only with no receipt and falls under 10 days leeway
            if(!$result && intval(Carbon::now()->format('d')) < 11){
                // With 1 month reimbursement and no submitted receipt(Must be in previous months, proceed due to 7 days leeway)
                if(count($months) == 1){
                    if($previous_month == (intval(Carbon::createFromFormat('F', $months[0])->format('m')) -1)) $result = true;
                }else{
                    //If total months that has reimbursement is more than 1,
                    // do additional checking, missing receipts must only be 1 month and must be previous month only.
                    // else will not proceed
                    if((count($months) - $dms_submitteds->count() == 1)){
                        $previous_submitted = ExpenseMonthlyDmsReceive::where('user_id',$user_id)
                            ->where('month',$previous_month)
                            ->where('year',$year)
                            ->first();
                        
                        $result = $previous_submitted ? false : true;
                    }
                }
            }
        // }

        return $result;
    }

    /**
     *  Check submitted receipts for previous months if complete
     *
     */
    public function generateMonths($start_month,$end_month,$year,$user_id){
        $months = [];
        $month_number = $start_month;

        for ($x = $start_month; $x <= $end_month; $x++) {
            if($this->checkIfHasSubmittedExpense($month_number,$year,$user_id)) $months[] = Carbon::createFromFormat('m', $month_number)->format('F');
            $month_number += 1;
        }
        return $months;
    }
    
    /**
     *  Check submitted receipts for previous months if complete
     *
     */
    public function checkIfHasSubmittedExpense($month,$year,$user_id){
        $expense = Expense::where('user_id',$user_id)
            ->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->where('expenses_entry_id','!=',0)
            ->where('status_id',3)
            ->first();

        return $expense ? true : false;
    }

    /**
     *  Check submitted receipts for previous months if complete
     *
     */
    public function recordForReposting($index,$grouped_expenses,$expense_from,$expense_to,$posting_date){
        Expense::whereIn('id',array_column($grouped_expenses,'id'))
            ->update([
                'expense_from' => $expense_from,
                'expense_to' => $expense_to,
                'should_be_posting_date' => $posting_date,
                'expense_grouping' => $posting_date->format('y-m-d').'+'.$grouped_expenses[0]->user_id.'+'.$index,
                'status_id' => 2
            ]);
    }

    /**
     *  Check if user has pending amount for deduction
     *
     */
    public function checkPendingAmountForDeduction($expense,$posting_date){
        $day = intval(Carbon::parse($posting_date)->format('d'));
        $current_month = Carbon::now()->format('F');
        $current_year = Carbon::now()->format('Y');
        $posting_month = Carbon::parse($posting_date)->format('F');
        $posting_year = Carbon::parse($posting_date)->format('Y');
        $previous_posting_month = Carbon::parse($posting_date)->subMonthsNoOverflow(1)->format('F');
        $previous_posting_year = Carbon::parse($posting_date)->subMonthsNoOverflow(1)->format('Y');

        // Activate deduction from previous months after 10 days leeway
        // if(intval(Carbon::parse($posting_date)->format('d')) > 10){    
            $monthly_expenses = EmployeeMonthlyExpense::where('user_id',$expense->user_id)
                ->where('balance_rejected_amount','>',0)
                ->when($day > 10,function($query) use($posting_month,$posting_year){
                    $query->where(function($q) use($posting_month,$posting_year){
                        $q->where('month','!=',$posting_month)
                        ->orWhere('year','!=',$posting_year);
                    });
                })
                ->when($day <= 10,function($query) use($posting_month,$posting_year,$previous_posting_month,$previous_posting_year){
                    $query->where(function($q) use($posting_month,$posting_year,$previous_posting_month,$previous_posting_year){
                        $q->where(function($q2) use($posting_month,$posting_year){
                            $q2->where('month','!=',$posting_month)
                            ->where('year','!=',$posting_year);
                        })
                        ->where(function($q2) use($previous_posting_month,$previous_posting_year){
                            $q2->where('month','!=',$previous_posting_month)
                            ->where('year','!=',$previous_posting_year);
                        });
                    });
                })
                ->where(function($q)use($current_month,$current_year){
                    $q->where('month','!=',$current_month)
                    ->orWhere('year','!=',$current_year);
                })
                ->get();
            
            if($monthly_expenses->isNotEmpty()){
                $expense_amount = ($expense->expense_rejected_reason_id == 4) ? ($expense->amount - $expense->rejected_deducted_amount) : $expense->amount;
                foreach($monthly_expenses as $monthly_expense){
                    if($expense_amount > 0){
                        $balance_rejected_amount = $monthly_expense->balance_rejected_amount;
                        $to_amount = 0;
                        $deducted_amount = $balance_rejected_amount;

                        if($balance_rejected_amount > $expense_amount){
                            $to_amount = $balance_rejected_amount - $expense_amount;
                            $deducted_amount = $balance_rejected_amount - $to_amount;
                        }

                        ExpenseDeduction::create([
                            'expense_id' => $expense->id,
                            'employee_monthly_expense_id' => $monthly_expense->id,
                            'balance_from_amount' => $balance_rejected_amount,
                            'balance_to_amount' => $to_amount,
                            'balance_deducted_amount' => $deducted_amount,
                            'expense_from_amount' => $expense_amount,
                            'expense_to_amount' => $expense_amount = ($expense_amount - $deducted_amount)
                        ]);

                        $monthly_expense->update([
                            'balance_rejected_amount' => $to_amount
                        ]);
                    }
                }

                // If expense amount is less than 1, Tagged expense as deducted,
                // else process remaining balanace
                if($expense_amount < 1){
                    Expense::find($expense->id)->update([
                        'status_id' => 4
                    ]);
                    return false;
                }else{
                    Expense::find($expense->id)->update([
                        'posted_amount' => $expense_amount
                    ]);

                    $expense->amount = $expense_amount;
                    return $expense;
                }
            }    
            return $expense;
        // }
        return $expense;
    }
}
