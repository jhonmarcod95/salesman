<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\APIExpenseResult as expenseResult;
use App\Http\Resources\SchedulesResource as SchedulesResource;
use App\Expense;
use App\ExpensesEntry;
use App\ExpensesType;
use App\Schedule;
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
    public function uploadExpensesReciept(Request $request, Expense $expense)
    {

        $all_headers = apache_request_headers();
        $newimg = file_put_contents(public_path('storage/expenses/') . $all_headers['File-Name'], file_get_contents('php://input'));

        $expense->attachment = 'expenses/'. $all_headers['File-Name'];
        $expense->save();

        return $expense;

    }

    public function updateExpense(Request $request, Expense $expense)
    {
        $this->validate($request, [
            'types' => 'required',
            'amount' => 'required'
        ]);

        $expense->amount = $request->input('amount');
        $expense->expensesType()->associate($request->input('types'));
        $expense->save();

        return new expenseResult(Expense::find($expense->id));

    }

    /**
     * Delete stored expense record
     *
     * @param Expense $expense
     * @return response
     */
    public function deleteExpense(Expense $expense)
    {
        try {

            $expense->delete();
            return response('Accepted', 202 )->header('Content-Type', 'application/json');

        } catch (\Exception $e) {

            return response('Connection Failure', 420)->header('Content-Type', 'application/json');

        }
    }

    public function sweepExpenses()
    {
        $sweepExpense = Exepense::where('user_id',Auth::user()->id)
                                ->where('created_at', Carbon::today())->delete();
    }

    // Expenses Entries App API

    public function storeExpensesEntries(Request $request)
    {
        $this->validate($request, [
            'expenses' => 'required',
            'totalExpenses' => 'required',
        ]);

        $expensesEntries = new ExpensesEntry;
        $expensesEntries->user_id = Auth::user()->id;
        $expensesEntries->expenses = json_encode($request->input('expenses'));
        $expensesEntries->totalExpenses = $request->input('totalExpenses');
        $expensesEntries->save();

        return $expensesEntries;

    }

    // Schedules App API

    public function getSchedules()
    {

        $schedules = Schedule::orderBy('id','DESC')
                        ->where('user_id', Auth::user()->id)
                        ->get();

         return SchedulesResource::collection($schedules);
    }

    public function completedToday()
    {

        $totalDaily = $this->dailySchedule()->count();
        $totalVisited = $this->dailySchedule()->where('status',1)->count();

        $progress = floor(( $totalVisited / $totalDaily ) * 100);

        return array('progress' => $progress);

    }

    public function dailySchedule()
    {

        $dailySchedule = Schedule::orderBy('id','DESC')
                            ->whereDate('date', Carbon::today())
                            ->where('user_id', Auth::user()->id)
                            ->get();

        return SchedulesResource::collection($dailySchedule);
    }

    public function markedVisited(Schedule $schedule)
    {

        $schedule->status = 1;
        $schedule->save();

        return $schedule;

    }


}
