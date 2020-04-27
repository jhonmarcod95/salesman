<?php

namespace App\Http\Controllers;

use Auth;
use App\{
    Message,
    Expense,
    ExpensesEntry,
    ExpensesType,
    User,
    SapUser,
    SapServer,
    PaymentHeader,
    PaymentHeaderError
};
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        
        if(Auth::user()->level() < 8  && !Auth::user()->hasRole('ap')){
            
            $expense = ExpensesEntry::with('user','expensesModel.payments')
            ->whereHas('user' , function($q){
                $q->whereHas('companies', function ($q){
                    $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            })
            ->whereDate('created_at', '>=',  $request->startDate)
            ->whereDate('created_at' ,'<=', $request->endDate)
            ->has('expensesModel')
            ->withCount('expensesModel')
            ->orderBy('id', 'desc')->get();
        }else{
            $expense = ExpensesEntry::with('user','expensesModel.payments')
                ->whereDate('created_at', '>=',  $request->startDate)
                ->whereDate('created_at' ,'<=', $request->endDate)
                ->has('expensesModel')
                ->withCount('expensesModel')
                ->orderBy('id', 'desc')->get();
        }

        $new_expense = [];
        // Executive and VP Roles
        if(Auth::user()->level() >= 6 || Auth::user()->hasRole('ap')){
            $new_expense = $expense; 
        }else{
            foreach($expense->pluck('user') as $key => $value){
                // AVP and Coordinator  roles
                if(Auth::user()->level() < 6){
                    if($value->roles[0]['level'] < 6){
                        $new_expense[] = $expense[$key]; 
                    }
                }else{
                    $new_expense = []; 
                }
            }
        }
        return $new_expense;
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
        return Expense::with('expensesType', 'payments')->whereHas('expensesEntry', function($q) use ($id){
            $q->where('id', $id);
        })->get();
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
                'reference_number' => Auth::user()->id . $reference_number + 1
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

        return PaymentHeader::with('paymentDetail', 'payments.expense', 'checkVoucher.checkInfo')
            ->where('company_name', $request->company)
            ->where(function ($q) use ($request){
                if ($request->weekFilter == '1'){ //posting date
                    $q->whereDate('created_at', '>=',  $request->startDate)
                        ->whereDate('created_at' ,'<=', $request->endDate);
                }
                else{ //expense date
                    $q->whereDate('expense_from', '>=',  $request->startDate)
                        ->whereDate('expense_to' ,'<=', $request->endDate);
                }

            })
            ->orderBy('id', 'desc')->get();
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
}
