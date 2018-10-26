<?php

namespace App\Http\Controllers;

use Auth;
use App\{
    Message,
    Expense,
    ExpensesEntry
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
        session(['header_text' => 'Expenses']);

        $notification = Message::where('user_id', '!=', Auth::user()->id)->whereNull('seen')->count();

        return view('expense.index', compact('notification'));
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

        $expense = ExpensesEntry::with('user')
                    ->whereDate('created_at', '>=',  $request->startDate)
                    ->whereDate('created_at' ,'<=', $request->endDate)
                    ->orderBy('id', 'desc')->get();

        return $expense;
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return array (ExpensesEntry::with('user')->findOrFail($id));

        $expenseEntry =  ExpensesEntry::findOrFail($id);
        $ids = collect(json_decode($expenseEntry->expenses, true))->pluck('expense_id');
        $expense = Expense::with('expensesType')->find($ids);

        return $expense;
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
