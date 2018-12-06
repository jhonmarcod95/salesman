<?php

namespace App\Http\Controllers;

use Auth;
use App\{
    Message,
    Expense,
    ExpensesEntry,
    ExpensesType
};
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
            
            $expense = ExpensesEntry::with('user')
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
            $expense = ExpensesEntry::with('user')
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
        
        $company = $request->company;
        if($company){
            $expense = ExpensesEntry::with('user')
            ->whereHas('user' , function($q) use($company){
                $q->whereHas('companies', function ($q) use($company){
                    $q->where('company_id', $company);
                });
            })->whereDate('created_at', '>=',  $request->startDate)
            ->whereDate('created_at' ,'<=', $request->endDate)
            ->has('expensesModel')
            ->withCount('expensesModel')
            ->orderBy('id', 'desc')->get();
        }else{
            $expense = ExpensesEntry::with('user')
            ->whereDate('created_at', '>=',  $request->startDate)
            ->whereDate('created_at' ,'<=', $request->endDate)
            ->has('expensesModel')
            ->withCount('expensesModel')
            ->orderBy('id', 'desc')->get();
        }

        return $expense;
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
        return ExpensesType::orderBy('id', 'desc')->get();
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
