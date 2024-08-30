<?php

namespace App\Http\Controllers;

use Auth;
use App\{
    Http\Controllers\APIController,
    Message,
    Expense,
    ExpenseMonthlyDmsReceive,
    ExpensesEntry,
    ExpensesType,
    User,
    SapUser,
    SapServer,
    PaymentHeader,
    PaymentHeaderError,
    SalesmanInternalOrder,
    ExpenseSapIoBudget,
    ExpenseVerificationStatus,
    ExpenseVerificationRejectedRemarks
};
use Carbon\Carbon;
use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;

use DB;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use ZipArchive;

class ExpenseController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session(['header_text' => 'Expenses Report']);

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('expense.index-report', compact('notification'));
    }

    /**
     * Get all Expenses by date
     *
     * @return \Illuminate\Http\Response
     */

    public function generateBydate(Request $request){   
        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);

        $verify_status = $request->expense_verify_status;
        $company = $request->company;
        
        if(Auth::user()->level() < 8  && !Auth::user()->hasRole('ap')) {
            // $expense = ExpensesEntry::with('user','expensesModel.payments')
            // ->whereHas('user' , function($q){
            //     $q->whereHas('companies', function ($q){
            //         $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
            //     });
            // })
            // ->whereDate('created_at', '>=',  $request->startDate)
            // ->whereDate('created_at' ,'<=', $request->endDate)
            // ->when($verify_status, function ($q) use ($verify_status) {
            //     if($verify_status == "verified") {
            //         $q->has('verifiedExpense');
            //     }
            //     if($verify_status == "unverified") {
            //         $q->doesntHave('verifiedExpense');
            //     }
            // })
            // ->has('expensesModel')
            // ->withCount('expensesModel')
            // ->withCount('verifiedExpense')
            // ->orderBy('id', 'desc')->get();

            $expensesWithEntries = ExpensesEntry::with('user:id,name,company_id,email','user.company:id,code,name','expensesModel.payments')
            ->whereHas('user' , function($q){
                $q->whereHas('companies', function ($q){
                    $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            })
            ->whereDate('created_at', '>=',  $request->startDate)
            ->whereDate('created_at' ,'<=', $request->endDate)
            ->has('expensesModel')
            ->when($verify_status, function ($q) use ($verify_status) {
                // if($verify_status == "unverified") {
                //     $q->doesntHave('verifiedExpense');
                // } else {
                //     $q->has('verifiedExpense');
                // }

                switch ($verify_status) {
                    case 1:
                        # Verified...
                        $q->has('verifiedExpense');
                        break;
                    case 3:
                        # Rejected...
                        $q->has('rejectedExpense');
                        break;
                    default:
                        # Pending and Unverified...
                        $q->has('pendingExpense');
                        $q->orHas('rejectedExpense');
                        break;
                }
            })
            ->withCount('expensesModel')
            ->withCount('verifiedExpense')
            ->withCount('unverifiedExpense')
            ->withCount('rejectedExpense')
            ->withCount('pendingExpense')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'tsr_name' => $expense->user ? $expense->user->name : '',
                    'company' => $expense->user->company ? $expense->user->company->name : '',
                    'expenses_model_count' => $expense->expenses_model_count,
                    'verified_expense_count' => $expense->verified_expense_count,
                    'unverified_expense_count' => $expense->unverified_expense_count,
                    'rejected_expense_count' => $expense->rejected_expense_count,
                    'pending_expense_count' => $expense->pending_expense_count,
                    'expenses_model' => $expense->expensesModel,
                    'user' => $expense->user,
                    'created_at' => Carbon::parse($expense->created_at)->format('M d, Y'),
                ];
            });


            $usersWithoutEntries = User::select('id','name','company_id','email')->with('company:id,code,name')->doesntHave('expensesEntries')
            ->whereHas('companies', function ($q) {
                $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
            })
            ->get()
            ->map(function ($user) {
                return [
                    'id' => null,
                    'tsr_name' => $user->name,
                    'company' => $user->company ? $user->company->name : '',
                    'expenses_model_count' => 0,
                    'verified_expense_count' => 0,
                    'expenses_model' => null,
                    'user' => $user,
                    'created_at' => null,
                ];
            });

            $expenses = collect($expensesWithEntries)->merge(collect($usersWithoutEntries))->sortByDesc('id')->values()->toArray();
        }else{

            // $expense = User::with(['expensesEntries' => function ($query) use ($request, $verify_status) {
            //     $query->whereDate('created_at', '>=',  $request->startDate)
            //           ->whereDate('created_at', '<=', $request->endDate)
            //           ->when($verify_status, function ($q) use ($verify_status) {
            //               if ($verify_status == "verified") {
            //                   $q->has('verifiedExpense');
            //               }
            //               if ($verify_status == "unverified") {
            //                   $q->doesntHave('verifiedExpense');
            //               }
            //           })
            //           ->withCount('expensesModel')
            //           ->withCount('verifiedExpense')
            //           ->orderBy('id', 'desc');
            // }])->get()
            // ->map(function ($user) {
            //     return [
            //         'id' => $user->id,
            //         'name' => $user->name,
            //         'email' => $user->email,
            //         // 'expenses_model_count' => $user->expensesEntries->expenses_model_count,
            //         'expenses_entries' => $user->expensesEntries
            //     ];
            // })
            // ->toArray();

            $expensesWithEntries = ExpensesEntry::with('user:id,name,company_id,email','user.company:id,code,name','expensesModel.payments')
            ->whereDate('created_at', '>=',  $request->startDate)
            ->whereDate('created_at' ,'<=', $request->endDate)
            ->when($company, function ($query) use ($company) {
                $query->whereHas('user', function($q) use ($company){
                    $q->whereHas('companies', function ($q) use ($company){
                        $q->where('company_id', $company);
                    });
                });
            })
            ->has('expensesModel')
            ->when($verify_status, function ($q) use ($verify_status) {
                // if($verify_status == "unverified") {
                //     $q->doesntHave('verifiedExpense');
                // } else {
                //     $q->has('verifiedExpense');
                // }

                switch ($verify_status) {
                    case 1:
                        # Verified...
                        $q->has('verifiedExpense');
                        break;
                    case 3:
                        # Rejected...
                        $q->has('rejectedExpense');
                        break;
                    default:
                        # Pending and Unverified...
                        $q->has('pendingExpense');
                        $q->orHas('unverifiedExpense');
                        break;
                }
            })
            ->withCount('expensesModel')
            ->withCount('verifiedExpense')
            ->withCount('unverifiedExpense')
            ->withCount('rejectedExpense')
            ->withCount('pendingExpense')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'tsr_name' => $expense->user ? $expense->user->name : '',
                    'company' => $expense->user->company ? $expense->user->company->name : '',
                    'expenses_model_count' => $expense->expenses_model_count,
                    'verified_expense_count' => $expense->verified_expense_count,
                    'unverified_expense_count' => $expense->unverified_expense_count,
                    'rejected_expense_count' => $expense->rejected_expense_count,
                    'pending_expense_count' => $expense->pending_expense_count,
                    'expenses_model' => $expense->expensesModel,
                    'user' => $expense->user,
                    'created_at' =>  Carbon::parse($expense->created_at)->format('M d, Y')
                ];
            });

            $usersWithoutEntries = User::select('id','name','company_id','email')->with('company:id,code,name')->doesntHave('expensesEntries')
            ->when($company , function($q) use ($company) {
                $q->whereHas('companies', function ($q) use ($company){
                    $q->where('company_id', $company);
                });
            })
            ->get()
            ->map(function ($user) {
                return [
                    'id' => null,
                    'tsr_name' => $user->name,
                    'company' => $user->company ? $user->company->name : '',
                    'expenses_model_count' => 0,
                    'verified_expense_count' => 0,
                    'unverified_expense_count' => 0,
                    'rejected_expense_count' => 0,
                    'pending_expense_count' => 0,
                    'expenses_model' => null,
                    'user' => $user,
                    'created_at' => null
                ];
            });

            \Log::info($usersWithoutEntries); 
            // Combine the results
            $expenses = collect($expensesWithEntries)->merge(collect($usersWithoutEntries))->sortByDesc('id')->values()->toArray();   
        }
        return $expenses;
    }

    /**
     * Get all Expenses by company
     *
     * @return \Illuminate\Http\Response
     */
    
    public function generateByCompany(Request $request){
        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate',
        ]);

        session(['dateEntry' => date("m/d/Y", strtotime($request->startDate)) . ' to ' . date("m/d/Y", strtotime($request->endDate))]);

        $company = $request->company;
        if($company){
            $expense = ExpensesEntry::with('user' ,'expensesModel.payments')
            ->whereHas('user' , function($q) use($company){
                $q->whereHas('companies', function ($q) use($company){
                    $q->where('company_id', $company);
                });
            })->whereDate('created_at', '>=',  $request->startDate)
            ->whereDate('created_at' ,'<=', $request->endDate)
            ->has('expensesModel')
            ->withCount('expensesModel')
            ->orderBy('id', 'desc')
            ->get();
          
        }else{
            $expense = ExpensesEntry::with('user', 'expensesModel.payments')
            ->whereDate('created_at', '>=',  $request->startDate)
            ->whereDate('created_at' ,'<=', $request->endDate)
            ->has('expensesModel')
            ->withCount('expensesModel')
            ->orderBy('id', 'desc')
            ->get();

        }

        return array($expense->groupBy('user_id'));
    }

    /**
     * Get all Expenses of user per week
     *
     * @return \Illuminate\Http\Response
     */
    public function generateBydatePerUser(Request $request, $ids){
        $explode_id = array_map('intval', explode(',', $ids));
        return Expense::with('user','user.companies','user.companies.businessArea','user.companies.glTaxcode','user.location','user.vendor','user.internalOrders','expensesType','expensesType.expenseChargeType.chargeType.expenseGl', 'payments','receiptExpenses','receiptExpenses.receiptType','routeTransportation')->whereHas('expensesEntry', function($q) use ($explode_id){
            $q->whereIn('id', $explode_id);
        })->get();
    }
    /**
     * Show Expense page
     *
     * @return \Illuminate\Http\Response
     */
    public function indexExpense(){
        session(['header_text' => 'Expenses']);

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('expense.index', compact('notification'));
    }

    /**
     * Get all expense
     *
     * @return \Illuminate\Http\Response
     */
    public function indexExpenseData(){
        return ExpensesType::with('expenseChargeType.chargeType')->orderBy('id', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $expensesType = new ExpensesType;

        $expensesType->name = $request->name;
        $expensesType->save();

        return $expensesType;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Expense::with('expensesType','payments','expenseVerificationStatus:id,name','expenseRejectedRemarks:id,remark')
            ->whereHas('expensesEntry', function($q) use ($id){
                $q->where('id', $id);
            })
            ->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpensesType $expensesType)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
        ]);

        $expensesType->name = $request->name;
        $expensesType->save();

        return $expensesType;
    }
    
    /**
     * Get Expense Submitted 
     *
     * @return \Illuminate\Http\Response
     */
    public function getExpenseSubmitted(Request $request){
        session(['expense_submitted_id' => $request->ids]);
        session(['current_week' => $request->current_week]);

        $expense = Expense::whereIn('expenses_entry_id', $request->ids)->get();
        return '/expense-submitted-page';
    
    }
    /**
     * Show Expense Submitted page
     *
     * @return \Illuminate\Http\Response
     */

    public function showExpenseSubmitted(){
        $date = session('dateEntry');
        $ids = session('expense_submitted_id');
        $currentWeek = session('current_week');
        return view("expense.index-submitted", compact('ids', 'date','currentWeek'));
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
            $sap_user = SapUser::where('user_id', Auth::user()->id)->where('sap_server', $sap_server->sap_server)->first();
            $header_query = PaymentHeader::where('ap_user', $sap_user->sap_id)->get()->last();
            $reference_number = '000000000';
            if($header_query){
                $reference_number = substr($header_query->reference_number, -9);
            }

            $data = [
                'sap_user' => $sap_user,
                'sap_server' => $sap_server,
                'reference_number' => Auth::user()->id . ($reference_number + 1)
            ];

            return array ($data);
        }
    } 
    
    /**
     * Posted expenses page
     *
     * @return \Illuminate\Http\Response
     */
    public function expensePostedIndex(){
        
        session(['header_text' => 'Posted Expenses']);

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('payment-posted.index', compact('notification'));
    }


    /**
     * Fetch all expense posted
     *
     * @return \Illuminate\Http\Response
     */

    public function expensePostedIndexData(Request $request){
        $request->validate([
            'company' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'weekFilter' => 'required'
        ]);
        // Work around to set end date for remaining days of the month
        $end_date = Carbon::parse($request->endDate);
        $last_day = Carbon::parse($request->endDate)->endOfMonth();
        // If remaining days is less than a week, set last day of the month as end date
        if($last_day->diffInDays($end_date) < 7){
            $end_date = $last_day;
        }

        return PaymentHeader::with(['paymentDetail', 'checkVoucher.checkInfo','payments' => function($q) use($request, $end_date){
                $q->when($request->weekFilter == '1', function($q) use($request, $end_date){//Posting
                    $q->whereDate('created_at', '>=',  $request->startDate)
                    ->whereDate('created_at' ,'<=', $end_date);
                })
                ->when($request->weekFilter == '2', function($q) use($request, $end_date){//Expense
                    $q->whereHas('expense',function($q) use($request,$end_date){
                        $q->whereDate('created_at', '>=',  $request->startDate)
                        ->whereDate('created_at' ,'<=', $end_date);
                    });
                })
                ->with('expense');
            }])
            ->where('company_name', $request->company)
            ->when($request->weekFilter == '1', function($q) use($request, $end_date){//posting date
                $q->whereDate('created_at', '>=',  $request->startDate)
                ->whereDate('created_at' ,'<=', $end_date);
            })
            ->when($request->weekFilter == '2', function($q) use($request,$end_date){//expense date
                $q->whereDate('expense_from', '>=',  $request->startDate)
                ->whereDate('expense_to' ,'<=', $end_date);
            })
            ->when($request->weekFilter == '3', function($q) use($request){//all
                $q->where('vendor_name', 'LIKE', '%' . $request->search . '%');
            })
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     *Unposted expenses page
     *
     * @return \Illuminate\Http\Response
     */
    public function expenseUnpostedIndex(){
        
        session(['header_text' => 'Unposted Expenses']);

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('payment-unposted.index', compact('notification'));
    }

    /**
     * Fetch all expense unposted
     *
     * @return \Illuminate\Http\Response
     */

    public function expenseUnpostedIndexData(Request $request){
        $request->validate([
            'company' => 'required',
            'startDate' => 'required',
            'endDate' => 'required'
        ]);
        
        $companyId = $request->company;
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $coveredWeek = Carbon::parse($startDate)->format('m/d/Y') . ' to ' .Carbon::parse($endDate)->format('m/d/Y');
        
        return PaymentHeaderError::with('paymentHeaderDetailError','user','user.expenses.expensesType')
        ->whereHas('user' , function($q) use ($companyId){
            $q->whereHas('companies', function($q) use ($companyId){
                $q->where('company_id', $companyId);
            });
        })->with(['user.expenses' => function ($q) use ($startDate, $endDate){
            $q->whereDate('created_at', '>=',  $startDate)->whereDate('created_at' ,'<=', $endDate);
        }])->where('cover_week','SALESFORCE REIMBURSEMENT; '. $coveredWeek)
        ->where('posting_type', 'POST')->orderBy('id', 'desc')->get();
    }

    public function historicalExpenseReport(){
        session(['header_text' => 'Expenses Report']);
        return view('expense.historical-expense-report');
    }

    public function historicalExpenseReportData(){

    }

    public function expenseTopSpender(){
        session(['header_text' => 'Expenses Report']);
        return view('expense.expense-top-spender');
    }

    public function expenseTopSpenderData(Request $request){

        $request->validate([
            'company' => 'required'
        ]);

        $data_request = $request->all();

        $data_request['year'] = $data_request['year'] ? $data_request['year'] : date('Y');
        $data_request['month'] = $data_request['month'] ? $data_request['month'] : date('m');

        $all_expenses_by_tsr = DB::table('users')
                                        ->select([
                                            'users.id',
                                            'users.name',
                                            'companies.name as company',
                                            DB::raw("SUM(expenses.amount) AS total_expenses_amount"),
                                        ])
                                        ->leftJoin('companies', function($q){
                                            $q->on('companies.id', '=', 'users.company_id');
                                        })
                                        ->leftJoin('payments', function($q){
                                            $q->on('users.id', '=', 'payments.user_id');
                                        })
                                        ->leftJoin('expenses', function($q){
                                            $q->on('expenses.id', '=', 'payments.expense_id');
                                        })
                                        ->whereNotNull('payments.document_code')
                                        ->where('users.company_id', $data_request['company'])
                                        ->whereYear('expenses.created_at',$data_request['year'])
                                        ->whereMonth('expenses.created_at',$data_request['month'])
                                        ->orderBy('users.name','ASC')
                                        ->groupBy('users.id','users.name','companies.name')
                                        ->get();
        
        $all_expenses_by_tsr_with_budget = [];

        if($all_expenses_by_tsr){
            foreach($all_expenses_by_tsr as $k => $tsr){
                $all_expenses_by_tsr_with_budget[$k] = $tsr;
                $salesman_internal_orders = SalesmanInternalOrder::with(['balanceHistory' => function ($query) use($data_request){
                                                                        $query->whereMonth('date',$data_request['month']);
                                                                        $query->whereYear('date',$data_request['year']);
                                                                    }])
                                                                    ->where('user_id', $tsr->id)->get();
                $total_amount_balance_history = 0;
                if(count($salesman_internal_orders) > 0){
                    $balance_history = 0;
                    foreach($salesman_internal_orders as $internal_order){
                        if(count($internal_order['balanceHistory']) > 0){
                            $balance_history += $internal_order['balanceHistory'][0] ? $internal_order['balanceHistory'][0]['from'] : 0;
                        }
                    }
                    $total_amount_balance_history = $balance_history;
                }
                $all_expenses_by_tsr_with_budget[$k]->monthly_total_budget = $total_amount_balance_history;
                
            }
        }

        return $all_expenses_by_tsr_with_budget;
    }

    public function expenseCurrentTopSpenderData(){

        $data_request = [];
        $data_request['company'] = Auth::user()->company_id;
        $data_request['year'] = date('Y');
        $data_request['month'] = date('m');

        $all_expenses_by_tsr = DB::table('users')
                                        ->select([
                                            'users.id',
                                            'users.name',
                                            'companies.name as company',
                                            DB::raw("SUM(expenses.amount) AS total_expenses_amount"),
                                        ])
                                        ->leftJoin('companies', function($q){
                                            $q->on('companies.id', '=', 'users.company_id');
                                        })
                                        ->leftJoin('payments', function($q){
                                            $q->on('users.id', '=', 'payments.user_id');
                                        })
                                        ->leftJoin('expenses', function($q){
                                            $q->on('expenses.id', '=', 'payments.expense_id');
                                        })
                                        ->whereNotNull('payments.document_code')
                                        ->where('users.company_id', $data_request['company'])
                                        ->whereYear('expenses.created_at',$data_request['year'])
                                        ->whereMonth('expenses.created_at',$data_request['month'])
                                        ->orderBy('users.name','ASC')
                                        ->groupBy('users.id','users.name','companies.name')
                                        ->get();

        $all_expenses_by_tsr_with_budget = [];

        if($all_expenses_by_tsr){
            foreach($all_expenses_by_tsr as $k => $tsr){
                $all_expenses_by_tsr_with_budget[$k] = $tsr;
                $salesman_internal_orders = SalesmanInternalOrder::with(['balanceHistory' => function ($query) use($data_request){
                                                                        $query->whereMonth('date',$data_request['month']);
                                                                        $query->whereYear('date',$data_request['year']);
                                                                    }])
                                                                    ->where('user_id', $tsr->id)->get();
                $total_amount_balance_history = 0;
                if(count($salesman_internal_orders) > 0){
                    $balance_history = 0;
                    foreach($salesman_internal_orders as $internal_order){
                        if(count($internal_order['balanceHistory']) > 0){
                            $balance_history += $internal_order['balanceHistory'][0] ? $internal_order['balanceHistory'][0]['from'] : 0;
                        }
                    }
                    $total_amount_balance_history = $balance_history;
                }
                $all_expenses_by_tsr_with_budget[$k]->monthly_total_budget = $total_amount_balance_history;
                
            }
        }

        return $all_expenses_by_tsr_with_budget;

    }

    public function expenseIOReport(){
        return view('expense.expense-io-report');
    }

    public function expenseIOReportData(Request $request){
        $data = $request->all();
        

        $expense_io_budget = DB::table('users')
                                    ->select([
                                        'users.id',
                                        'users.name',
                                        'companies.name as company',
                                        DB::raw("SUM(expense_sap_io_budgets.planned_budget) AS total_planned_budget"),
                                        DB::raw("SUM(expense_sap_io_budgets.budget_balance) AS total_budget_balance"),
                                    ])
                                    ->leftJoin('companies', function($q){
                                        $q->on('companies.id', '=', 'users.company_id');
                                    })
                                    ->leftJoin('expense_sap_io_budgets', function($q){
                                        $q->on('expense_sap_io_budgets.user_id', '=', 'users.id');
                                    })
                                    ->where('users.company_id', $data['company'])
                                    ->where('expense_sap_io_budgets.io_date',$data['year'] . $data['month'] . '01')
                                    ->orderBy('users.name','ASC')
                                    ->groupBy('users.id','users.name','companies.name')
                                    ->get();
        return $expense_io_budget;
    }


    public function expenseIOChecker(){

        $sapConnection = [
            'ashost' => '172.17.1.33',
            'sysnr' => '00',
            'client' => '400',
            'user' => 'payproject',
            'passwd' => 'welcome69'
        ];
        
        $all_tsr = User::with('company')->where('company_id', Auth::user()->company_id)->orderBy('name','ASC')->take(10)->get();

        $sapConnection = [
            'ashost' => '172.17.1.33',
            'sysnr' => '00',
            'client' => '400',
            'user' => 'payproject',
            'passwd' => 'welcome69'
        ];

        $month = date('07');
        $year = date('Y');

        $tsr_arr = [];
        foreach($all_tsr as $k => $tsr){

            $tsr_arr[$k]['name'] = $tsr['name'];

            //PFMC
            $io_pfmc = SalesmanInternalOrder::where('user_id', $tsr['id'])->where('sap_server','PFMC')->get();
            $expense_tsr_pfmc = [];
            $ioBalances = [];
            if($io_pfmc){
                foreach($io_pfmc as $k => $tsr_io){
                
                    $io = $tsr_io['internal_order'];
                    try{
                        $budget = APIController::executeSapFunction($sapConnection, 'ZFI_BUDGET_CHK_INTEG', [
                            'P_AUFNR' => $io,
                            'P_BUDAT' => $year . $month . '01',    
                        ],null);
                    }catch (RequestException $e){
                        return $budget = [];
                    }
                   
        
                    $expense_tsr_pfmc[$k]['io'] = $io;
                    $expense_tsr_pfmc[$k]['date'] = $year . $month . '01';
                    $expense_tsr_pfmc[$k]['budget'] = $budget;

                }
                $tsr_arr[$k]['budget'] = $budget;
            }else{
                $tsr_arr[$k]['budget'] = [];
            }

            
        }

        return $tsr_arr;
    }

    public function verifyAttachment(Request $request, $expenseId) {
        if($request->mode == 'reject') {
            $request->validate([
                'rejected_reason_id' => 'required',
                'deducted_amount' => 'required_if:rejected_reason_id,4',
            ], [
                'rejected_reason_id.required' => "Reject reason field is required.",
                'deducted_amount.required_if' => "Input amount to be deducted from declared amount.",
            ]);
        }

        $user_id =  Auth::user()->id;
        $rejected_id = isset($request->rejected_reason_id) ? $request->rejected_reason_id : null;
        $deducted_amount = null;
        $date = now();

        switch ($request->mode) {
            case 'unset':
                $status = 0;
                $user_id = null;
                $date = null;
                break;
            case 'verify':
                $status = 1;
                break;
            case 'unverify':
                $status = 2;
                break;
            case 'reject':
                $status = 3;
                $deducted_amount = $rejected_id == 4 ? (int) $request->deducted_amount : null;
                break;
        }

        Expense::find($expenseId)->update([
            'verified_status_id' => $status,
            'expense_rejected_reason_id' => $rejected_id,
            'rejected_deducted_amount' => $deducted_amount,
            'verified_by' => $user_id,
            'date_verified' => $date,
        ]);
    }

    public function getExpenseRejectedRemarks() {
        return ExpenseVerificationRejectedRemarks::all(['id','remark']);
    }

    public function getExpenseVerificationStatuses() {
        return ExpenseVerificationStatus::all(['id','name']);
    }

    //DMS Received Report ================================================
    public function dmsReceivedReportIndex() {
        session(['header_text' => 'Expenses Report']);
        return view('expense.dms-received-index-report');
    }

    public function getUserStatPerMonth($user_id, $month, $year) {
        $dms_month_year = "$month $year";
        $first_of_month = date('Y-m-d', strtotime("first day of $dms_month_year"));
        $last_of_month = date('Y-m-d', strtotime("last day of $dms_month_year"));
        $start_date = "$first_of_month 00:00:01";
        $last_date = "$last_of_month 23:59:59";

        $expenses_entry = ExpensesEntry::where('user_id', $user_id)
            ->withCount('verifiedExpense')
            ->withCount('unverifiedExpense')
            ->withCount('rejectedExpense')
            ->withCount('pendingExpense')
            ->withCount('expensesModel')
            ->whereBetween('created_at', [$start_date, $last_date])
            ->get();

        $not_verified_expense_count = 0;
        $verified_expense_count = 0;
        $unverified_expense_count = 0;
        $rejected_expense_count = 0;
        $total_expense_count = 0;

        foreach ($expenses_entry as $expense) {
            $verified_expense_count = $verified_expense_count + $expense->verified_expense_count;
            $unverified_expense_count = $unverified_expense_count + $expense->unverified_expense_count;
            $rejected_expense_count = $rejected_expense_count + $expense->rejected_expense_count;
            $not_verified_expense_count = $not_verified_expense_count + $expense->pending_expense_count;
            $total_expense_count = $total_expense_count + $expense->expenses_model_count;
        }

        return [
            'not_verified' => $not_verified_expense_count,
            'verified' => $verified_expense_count,
            'unverified' => $unverified_expense_count,
            'rejected' => $rejected_expense_count,
            'expense_count' => $total_expense_count
        ];
    }

    public function dmsReceivedReportAll(Request $request) {
        $expenseMonthlyDmsReceive = ExpenseMonthlyDmsReceive::with('user:id,name', 'user.companies', 'user.expenses')
            ->when(isset($request->user_id), function($q) use($request){
                $q->whereHas('user', function($userQuery) use ($request){
                    $userQuery->where('id', $request->user_id);
                });
            })
            ->when(isset($request->month_year), function($q) use($request){
                $date = date('Y-m-t 23:59:59', strtotime($request->month_year));
                $month = date('F', strtotime($date));
                $year = date('Y', strtotime($date));
                $q->where(['month' => $month, 'year' => $year]);
            })
            ->whereHas('user', function ($q) {
                $q->whereHas('companies', function ($q) {
                    if (!Auth::user()->hasRole('it')) {
                        $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                    }
                });
            })
            ->paginate($request->limit);

        $expenseMonthlyDmsReceive->getCollection()->transform(function($item) {
            $item['expense_status'] = $this->getUserStatPerMonth($item['user_id'], $item['month'], $item['year']);
            return $item;
        });

        return $expenseMonthlyDmsReceive;
    }
    //====================================================================

}
