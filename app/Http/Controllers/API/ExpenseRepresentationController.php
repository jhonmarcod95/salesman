<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\APIExpenseResult as expenseResult;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ExpenseRepresentation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Rules\AmountLimit;
use App\Expense;

class ExpenseRepresentationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $this->validate($request, [
            'purpose' => 'required',
            'expense_id' => 'required',
            'attendeesArray' => 'required'
        ]);

        // $attendeesArray = json_decode($request->input('attendeesArray'), true);

        $expenseRepresentation = new ExpenseRepresentation();
        $expenseRepresentation->user_id = Auth::user()->id;
        $expenseRepresentation->attendees = $request->input('attendeesArray');
        $expenseRepresentation->purpose = $request->input('purpose');
        $expenseRepresentation->expense()->associate($request->input('expense_id'));
        $expenseRepresentation->save();


        return $expenseRepresentation;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
