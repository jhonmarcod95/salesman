<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\APIExpenseResult as expenseResult;
use App\Expense;
use App\ExpensesType;
use Carbon\Carbon;


class AppAPIController extends Controller
{
    // Expenses App API

    public function getExpenses()  {
        $expenses = Expense::where('user_id',Auth::user()->id)->get();
        return expenseResult::collection($expenses);
    }

    /**
     * Get expenses types API
     *
     * @return json
     */
    public function getExpensesType() {
        $expensesType = ExpensesType::all();
        return $expensesType;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeExpenses(Request $request)
    {
        $this->validate($request, [
            'types' => 'required',
            'amount' => 'required'
        ]);

        $expense = new Expense();
        $expense->user()->associate(Auth::user()->id);
        $expense->amount = $request->input('amount');
        $expense->expensesType()->associate($request->input('types'));
        $expense->save();

        return new expenseResult(Expense::find($expense->id));

    }

    /**
     * Upload expense reciept
     *
     * @param Expense $expense
     * @return response
     */
    public function uploadExpensesReciept(Expense $expense)
    {
        $this->validate($request, [
            'attachement' => 'required'
        ]);

        $expense->attachement = $request->file('attachment')->store('expenses','public');
        $expense->save();

        return $expense;

    }


}
