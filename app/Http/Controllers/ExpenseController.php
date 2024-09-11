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
use App\Rules\ExpenseDeductionRule;
use Carbon\Carbon;
use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;

use DB;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use phpDocumentor\Reflection\Types\This;
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
     * Handle common query for expense per user
     *
     */
    public function expensePerUserCommonQuery($request) {
        $start_date = "$request->start_date 00:00:01";
        $end_date = "$request->end_date 23:59:59";
        $company = $request->company;
        $verify_status = $request->expense_verify_status;

        return User::select('id', 'name', 'company_id', 'email')
            ->with('company:id,code,name', 'roles', 'expensesEntries')
            ->when(isset($request->user_id), function($q) use($request){
                $q->where('id', $request->user_id);
            })
            ->when(Auth::user()->level() < 8  && !Auth::user()->hasRole('ap'), function($q) {
                $q->whereHas('companies', function ($companQuery){
                    $companQuery->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            }, function($q) use($company) {
                $q->when($company, function ($query) use ($company) {
                    $query->whereHas('companies', function ($companQuery) use ($company){
                        $companQuery->where('company_id', $company);
                    });
                });
            })
            ->whereHas('roles', function($q) {
                $q->whereIn('role_id', [4,5,6,7,8,9,10]);
            })
            ->when($request->expense_option != 'all', function($q) use($request,$start_date, $end_date) {
                if ($request->expense_option == 'with_expenses') {
                    $q->has('expensesEntries')
                    ->whereHas('expensesEntries', function ($query) use ($start_date, $end_date) {
                        $query->whereBetween('created_at',  [$start_date, $end_date]);
                     });
                } else {
                    $q->whereDoesntHave('expensesEntries');
                }
            })
            ->with(['expensesEntries' => function($q) use($start_date, $end_date) {
                $q->whereBetween('created_at',  [$start_date, $end_date])
                ->withCount('expensesModel')
                ->withCount('verifiedExpense')
                ->withCount('unverifiedExpense')
                ->withCount('rejectedExpense')
                ->withCount('pendingExpense');
            }])

            //Require only users with Expenses Entries when filtering verify status
            ->when(isset($verify_status), function($q) use($verify_status, $start_date, $end_date){
                $q->whereHas('expensesEntries', function ($query) use ($verify_status, $start_date, $end_date) {
                    $query->whereBetween('created_at',  [$start_date, $end_date])
                     ->whereHas('expensesModel', function($q2) use($verify_status){
                        $q2->where('verified_status_id',  $verify_status);
                    });
                });
            });
    }

    /**
     * Get all Expenses by user
     *
     * @return \Illuminate\Http\Response
     */
     public function getExpensePerUser(Request $request) {
        $userExpense = ($this->expensePerUserCommonQuery($request))->orderBy('name', 'ASC')->paginate($request->limit);

        $userExpense->getCollection()->transform(function($item) {
            if(!$item) return;

            $expenses_model_count   = 0;
            $verified_expense_count = 0;
            $unverified_expense_count = 0;
            $rejected_expense_count = 0;
            $total_expenses         = 0;
            $verified_amount        = 0;
            $rejected_amount        = 0;

            if(count($item->expensesEntries)) {
                foreach($item->expensesEntries as $expenses) {
                    $expenses_model_count     = $expenses_model_count + $expenses->expenses_model_count;
                    $verified_expense_count   = $verified_expense_count + $expenses->verified_expense_count;
                    $unverified_expense_count = $unverified_expense_count + ($expenses->unverified_expense_count + $expenses->pending_expense_count);
                    $rejected_expense_count   = $rejected_expense_count + $expenses->rejected_expense_count;
                    $total_expenses           = $total_expenses + $expenses->totalExpenses;

                    $verified = $this->computeVerifiedAndRejected($expenses->expensesModel);
                    $verified_amount = $verified_amount + $verified['verified_amount'];
                    $rejected_amount = $rejected_amount + $verified['rejected_amount'];
                }
            }

            $data['id'] = $item->id;
            $data['name'] = $item->name;
            $data['company'] = isset($item->company) ? $item->company->name : '-';
            $data['expense_entry_count'] = count($item->expensesEntries);
            $data['expenses_model_count'] = count($item->expensesEntries) ? $expenses_model_count : 0;
            $data['verified_expense_count'] = $verified_expense_count;
            $data['unverified_expense_count'] = $unverified_expense_count;
            $data['rejected_expense_count'] = $rejected_expense_count;
            $data['total_expenses'] = $total_expenses;
            $data['verified_amount'] = $verified_amount;
            $data['rejected_amount'] = $rejected_amount;
            $data['roles'] = $item->roles;
            return $data;
        });

        return $userExpense;
     }

     public function getExpenseVerifiedStat(Request $request) {
        $userExpenses = ($this->expensePerUserCommonQuery($request))->has('expensesEntries')->get();

        $expenses_model_count   = 0;
        $verified_expense_count = 0;
        $unverified_expense_count = 0;
        $rejected_expense_count = 0;

        $verified_amount = 0;
        $rejected_amount = 0;
        $total_expenses = 0;

        foreach($userExpenses as $item) {
            if (count($item->expensesEntries)) {
                foreach ($item->expensesEntries as $expenses) {
                    $expenses_model_count     = $expenses_model_count + $expenses->expenses_model_count;
                    $verified_expense_count   = $verified_expense_count + $expenses->verified_expense_count;
                    $unverified_expense_count = $unverified_expense_count + ($expenses->unverified_expense_count + $expenses->pending_expense_count);
                    $rejected_expense_count   = $rejected_expense_count + $expenses->rejected_expense_count;
                    $total_expenses           = $total_expenses + $expenses->totalExpenses;

                    $verified = $this->computeVerifiedAndRejected($expenses->expensesModel);
                    $verified_amount = $verified_amount + $verified['verified_amount'];
                    $rejected_amount = $rejected_amount + $verified['rejected_amount'];
                }
            }
        }

        $verified_percentage = $expenses_model_count ? ($verified_expense_count / $expenses_model_count) * 100 : 0;
        $rejected_percentage = $expenses_model_count ? ($rejected_expense_count / $expenses_model_count) * 100 : 0;
        $unverified_percentage = $expenses_model_count ?($unverified_expense_count / $expenses_model_count) * 100 : 0;

        return [
            'expenses_model_count' => $expenses_model_count,
            'verified_expense_count' => $verified_expense_count,
            'unverified_expense_count' => $unverified_expense_count,
            'rejected_expense_count' => $rejected_expense_count,
            'verified_amount' => $verified_amount,
            'rejected_amount' => $rejected_amount,
            'total_expenses' => $total_expenses,
            'verified_percentage' => round($verified_percentage),
            'unverified_percentage' => round($unverified_percentage),
            'rejected_percentage' => round($rejected_percentage),
        ];
     }

     public function computeVerifiedAndRejected($expenses) {
        $verified_amount = 0;
        $rejected_amount = 0;
        foreach ($expenses as $expense) {
            if ($expense->verified_status_id == 1) {
                $verified_amount = $verified_amount + $expense->amount;
            }

            if ($expense->verified_status_id == 3) {
                // compute rejected with remarks no.4
                if ($expense->expense_rejected_reason_id == 4) {
                    $rejected_amount = $rejected_amount + $expense->rejected_deducted_amount;

                    //Add remaining amount to approved amount after deduction
                    $verified_amount = $verified_amount + ($expense->amount - $expense->rejected_deducted_amount);
                } else {
                    $rejected_amount = $rejected_amount + $expense->amount;
                }
            }
        }

        return [
            'verified_amount' => $verified_amount,
            'rejected_amount' => $rejected_amount
        ];
     }
    
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
    public function show($id) /**TODO: REMOVE*/
    {
        return Expense::with('expensesType','payments','expenseVerificationStatus:id,name','expenseRejectedRemarks:id,remark', 'representaion:id,expense_id,attendees,purpose', 'verifier:id,name')
            ->whereHas('expensesEntry', function($q) use ($id){
                $q->where('id', $id);
            })
            ->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show2(Request $request, $user_id) {
        $start_date = "$request->start_date 00:00:01";
        $end_date = "$request->end_date 23:59:59";

        $last_day_of_last_month = date("Y-m-t 23:59:59", strtotime("last day of last month"));
        $first_day_of_last_month = date("Y-m-t 00:00:1", strtotime("first day of last month"));

        $expenses = Expense::with(
                'expensesType', 
                'payments', 
                'expenseVerificationStatus:id,name',
                'expenseRejectedRemarks:id,remark',
                'representaion:id,expense_id,attendees,purpose',
                'verifier:id,name')
            ->where('user_id', $user_id)
            ->whereHas('expensesEntry', function ($q) use ($start_date, $end_date) {
                $q->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->get();

        return $expenses->transform(function($item) use($last_day_of_last_month, $first_day_of_last_month){
            //Set default verification perion expired as false
            $item['verification_perion_expired'] = 0;

            //get expense date
            $expense_date = date('Y-m-t h:m:s', strtotime($item->created_at));

            //If expense date is past the first day of last month, the verification period will be expired
            if (strtotime($first_day_of_last_month) > strtotime($expense_date)) {
                $item['verification_perion_expired'] = 1;
            }

            if(date('d') > '07') {
                //Today is past 7th day of current montt,
                //If the expense date is past of last day of last month, the verification period will be expired
                if(strtotime($last_day_of_last_month) > strtotime($expense_date)) {
                    $item['verification_perion_expired'] = 1;
                }
            }

            return $item;
        });
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

        $same_month = true;
        $start_date = Carbon::parse($request->startDate);
        $end_date = Carbon::parse($request->endDate);
        $start_date2 = '';
        $end_date2 = '';

        if(!$start_date->isSameMonth($end_date)){
            $same_month = false;
            $end_date = Carbon::parse($request->startDate)->endOfMonth()->format('Y-m-d');
            $start_date2 = Carbon::parse($request->endDate)->startOfMonth()->format('Y-m-d');
            $end_date2 = $request->endDate;
        }

        return PaymentHeader::with(['paymentDetail', 'checkVoucher.checkInfo','payments' => function($q) use($request, $end_date,$same_month,$start_date2,$end_date2){
                $q->when($request->weekFilter == '1', function($q) use($request, $end_date){//Posting
                    $q->whereDate('created_at', '>=',  $request->startDate)
                    ->whereDate('created_at' ,'<=', $end_date);
                })
                ->when($request->weekFilter == '2', function($q) use($request, $end_date,$same_month,$start_date2,$end_date2){//Expense
                    $q->whereHas('expense',function($q) use($request,$end_date,$same_month,$start_date2,$end_date2){
                        $q->whereDate('created_at', '>=',  $request->startDate)
                        ->whereDate('created_at' ,'<=', $end_date)
                        ->orWhere(function ($q2)use($same_month,$start_date2,$end_date2){
                            $q2->when(!$same_month,function($q3) use ($start_date2,$end_date2){
                                $q3->whereDate('expense_from', '>=',  $start_date2)
                                ->whereDate('expense_to' ,'<=', $end_date2);
                            });
                        });
                    });
                })
                ->with('expense');
            }])
            ->where('company_name', $request->company)
            ->when($request->weekFilter == '1', function($q) use($request, $end_date){//posting date
                $q->whereDate('created_at', '>=',  $request->startDate)
                ->whereDate('created_at' ,'<=', $end_date);
            })
            ->when($request->weekFilter == '2', function($q) use($request,$end_date,$same_month,$start_date2,$end_date2){//expense date
                $q->whereDate('expense_from', '>=',  $request->startDate)
                ->whereDate('expense_to' ,'<=', $end_date)
                ->orWhere(function ($q2)use($same_month,$start_date2,$end_date2){
                    $q2->when(!$same_month,function($q3) use($start_date2,$end_date2){
                        $q3->whereDate('expense_from', '>=',  $start_date2)
                        ->whereDate('expense_to' ,'<=', $end_date2);
                    });
                });
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
                'deducted_amount' => ['required_if:rejected_reason_id,4', new ExpenseDeductionRule($expenseId)],
            ], [
                'rejected_reason_id.required' => "Reject reason field is required.",
                'deducted_amount.required_if' => "Input amount to be deducted from declared amount.",
            ]);
        }

        $user_id =  Auth::user()->id;
        $rejected_id = null;
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
                $rejected_id = isset($request->rejected_reason_id) ? $request->rejected_reason_id : null;
                $deducted_amount = $rejected_id == 4 ? (double) $request->deducted_amount : null;
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

        $verified_expense_count = 0;
        $unverified_expense_count = 0;
        $rejected_expense_count = 0;
        $total_expense_count = 0;

        foreach ($expenses_entry as $expense) {
            $verified_expense_count = $verified_expense_count + $expense->verified_expense_count;
            $unverified_expense_count = $unverified_expense_count + ($expense->unverified_expense_count + $expense->pending_expense_count);
            $rejected_expense_count = $rejected_expense_count + $expense->rejected_expense_count;
            $total_expense_count = $total_expense_count + $expense->expenses_model_count;
        }

        return [
            'verified' => $verified_expense_count,
            'unverified' => $unverified_expense_count,
            'rejected' => $rejected_expense_count,
            'expense_count' => $total_expense_count
        ];
    }

    public function dmsReceivedReportCommonQuery($request) {
        $company_id = $request->company_id;
        return ExpenseMonthlyDmsReceive::when(isset($request->user_id), function ($q) use ($request) {
                $q->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('id', $request->user_id);
                });
            })
            ->when(isset($request->month_year), function ($q) use ($request) {
                $date = date('Y-m-t 23:59:59', strtotime($request->month_year));
                $month = date('F', strtotime($date));
                $year = date('Y', strtotime($date));
                $q->where(['month' => $month, 'year' => $year]);
            })
            ->whereHas('user', function ($q) use($company_id) {
                $q->whereHas('companies', function ($q) use ($company_id){
                    if (isset($company_id)) {
                        $q->where('company_id', $company_id);
                    } else {
                        if (!Auth::user()->hasRole('it')) {
                            $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                        }
                    }
                });
            });
    }

    public function dmsReceivedReportAll(Request $request) {
        $expenseMonthlyDmsReceive = ($this->dmsReceivedReportCommonQuery($request))->with('user:id,name', 'user.companies', 'user.expenses')->paginate($request->limit);
        $expenseMonthlyDmsReceive->getCollection()->transform(function ($item) {
            $item['expense_status'] = $this->getUserStatPerMonth($item['user_id'], $item['month'], $item['year']);
            return $item;
        });

        return $expenseMonthlyDmsReceive;
    }

    public function dmsPendingReceivedReportAll(Request $request) {
        $expenseUserMonthlyDmsReceive = ($this->dmsReceivedReportCommonQuery($request))->select('user_id')->get()->pluck('user_id')->toArray();
        
        $first_day = date('Y-m-01 00:00:01', strtotime($request->month_year));
        $last_day = date('Y-m-t 23:59:59', strtotime($request->month_year));
        $company_id = $request->company_id;
        $user_id = $request->user_id;

        //Get MOnth and year
        $date = date('Y-m-t 23:59:59', strtotime($request->month_year));
        $month = date('F', strtotime($date));
        $year = date('Y', strtotime($date));

        //Get user who does not submit expense to DMS ========================
        $noDmsExpensesUserIds = ExpensesEntry::select('id', 'user_id')
            ->when(isset($user_id), function($q) use($user_id, $expenseUserMonthlyDmsReceive){
                if(empty($expenseUserMonthlyDmsReceive)) {
                    $q->where('user_id', $user_id);
                } else {
                    //Filter invalid id to return null
                    $q->where('user_id', '--');
                }
            })
            ->when(!isset($user_id), function($q) use($expenseUserMonthlyDmsReceive){
                //Add default invalid user id if no dms received match
                if (empty($expenseUserMonthlyDmsReceive)) {
                    $expenseUserMonthlyDmsReceive[0] = 0;
                }
                $q->whereNotIn('user_id', $expenseUserMonthlyDmsReceive);
            })
            ->when(isset($request->month_year), function($q) use($first_day, $last_day){
                $q->whereBetween('created_at', [$first_day, $last_day]);
            })
            ->whereHas('user', function ($q) use($company_id){
                $q->whereHas('companies', function ($q) use($company_id){
                    if(isset($company_id)) {
                        $q->where('company_id', $company_id);
                    } else {
                        if (!Auth::user()->hasRole('it')) {
                            $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                        }    
                    }
                });
            })
            ->get()
            ->unique('user_id')
            ->pluck('user_id')
            ->toArray();
        //===================================================================

        $noDmsExpensesUser = User::select('id','name')->with('companies')->whereIn('id', $noDmsExpensesUserIds)->paginate($request->limit);
        $noDmsExpensesUser->getCollection()->transform(function ($item) use($month, $year){
            $item['expense_status'] = $this->getUserStatPerMonth($item['id'], $month, $year);
            $item['month'] = $month;
            $item['year'] = $year;
            return $item;
        });

        return $noDmsExpensesUser;
    }

    public function noClaimedExpenses(Request $request) {
        $first_day = date('Y-m-01 00:00:01', strtotime($request->month_year));
        $last_day = date('Y-m-t 23:59:59', strtotime($request->month_year));
        $company_id = $request->company_id;

        //Get MOnth and year
        $date = date('Y-m-t 23:59:59', strtotime($request->month_year));
        $month = date('F', strtotime($date));
        $year = date('Y', strtotime($date));

        $noCalimedExpensesUser =  User::select('id', 'name', 'company_id', 'email')
            ->with('company:id,code,name', 'roles', 'expensesEntries')
            ->when(isset($request->user_id), function($q) use($request){
                $q->where('id', $request->user_id);
            })
            ->when(Auth::user()->level() < 8  && !Auth::user()->hasRole('ap'), function($q) {
                $q->whereHas('companies', function ($companQuery){
                    $companQuery->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            }, function($q) use($company_id) {
                $q->when(isset($request->company), function ($query) use ($company_id) {
                    $query->whereHas('companies', function ($companQuery) use ($company_id){
                        $companQuery->where('company_id', $company_id);
                    });
                });
            })
            ->whereHas('roles', function($q) {
                $q->whereIn('role_id', [4,5,6,7,8,9,10]);
            })
            ->whereDoesntHave('expensesEntries', function($q) use($first_day, $last_day){
                $q->whereBetween('created_at',  [$first_day, $last_day]);
            })
            ->orderBy('name', 'ASC')
            ->paginate($request->limit);

        $noCalimedExpensesUser->getCollection()->transform(function ($item) use ($month, $year) {
            $item['month'] = $month;
            $item['year'] = $year;
            return $item;
        });

        return $noCalimedExpensesUser;
    }
    //====================================================================

}
