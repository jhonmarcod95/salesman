<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\APIExpenseResult as expenseResult;
use App\Http\Resources\SchedulesResource as SchedulesResource;
use App\Http\Resources\ExpensesEntriesResult as ExpensesEntriesResult;
use App\Http\Resources\PaymentsResource as PaymentsResource;
use App\Expense;
use App\ExpensesEntry;
use App\ExpensesType;
use App\Schedule;
use App\Attendance;
use App\Payment;
use App\RequestSchedule;
use Carbon\Carbon;
use DB;


class AppAPIController extends Controller
{
    // Expenses App API

    public function getExpenses()  {
        $expenses = Expense::where('user_id',Auth::user()->id)
                        ->where('expenses_entry_id', 0)
                        ->get();
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

        $newimg = file_put_contents(public_path('storage/expenses/') . $request->header('File-Name'), file_get_contents('php://input'));

        $expense->attachment = 'expenses/'. $request->header('File-Name');
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
            'expenseId' => 'required',
        ]);

        $expensesEntries = new ExpensesEntry;
        $expensesEntries->user_id = Auth::user()->id;
        $expensesEntries->expenses = json_encode($request->input('expenses'));
        $expensesEntries->totalExpenses = $request->input('totalExpenses');
        $expensesEntries->save();

        Expense::whereIn('id', $request->input('expenseId'))
                ->update(['expenses_entry_id' => $expensesEntries->id]);

        return new ExpensesEntriesResult(ExpensesEntry::find($expensesEntries->id));

    }

    public function expensesEntries()
    {

        $expensesEntries = ExpensesEntry::
                            where('user_id', Auth::user()->id)
                            ->whereBetween('created_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
                            ->has('expensesModel')
                            ->get();

        return ExpensesEntriesResult::collection($expensesEntries);

    }

    public function showExpensesEntries($expensesEntries)
    {
        $showExpensesEntries = ExpensesEntry::where('user_id', Auth::user()->id)
                                    ->where('id', $expensesEntries)
                                    ->first();

        $expenses  = Expense::where('expenses_entry_id', $showExpensesEntries->id)->get();
        return expenseResult::collection($expenses);

    }

    // Schedules App API

    public function getCurrentSchedule()
    {
        $currentSchedule = Schedule::where('user_id', Auth::user()->id)
                        ->orderBy('id','DESC')
                        ->where('isCurrent', 1)
                        ->first();

        return $currentSchedule;
    }

    public function getSchedules() {

        $schedules = Schedule::orderBy('id','ASC')
                        ->where('date', '>=', Carbon::today())
                        ->where('user_id', Auth::user()->id)
                        ->take(25)
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

    public function checkHasSchedule()
    {
        $dailySchedule = Schedule::orderBy('id','DESC')
                            ->whereDate('date', Carbon::today())
                            ->where('user_id', Auth::user()->id)
                            ->exists();

        return response()->json($dailySchedule);

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

    // Attendance API

    public function signIn(Request $request)
    {

        file_put_contents(public_path('storage/attendance/') . $request->header('File-Name'), file_get_contents('php://input'));

        $attendance = new Attendance;
        $attendance->sign_in_image = 'attendance/'. $request->header('File-Name');
        $attendance->sign_in = Carbon::now();
        $attendance->sign_in_latitude = $request->header('Latitude');
        $attendance->sign_in_longitude = $request->header('Longitude');
        $attendance->sign_in_speed = $request->header('Speed');
        $attendance->user_id = Auth::user()->id;
        $attendance->schedule_id = $request->header('ScheduleId');
        $attendance->save();

        $schedule = Schedule::find($request->header('ScheduleId'));
        $schedule->isCurrent =  1;
        $schedule->save();

        // $this->validate($request, [
        //     'schedule' => 'required'
        // ]);

        // $attendance = new Attendance;
        // $attendance->sign_in = Carbon::now();
        // $attendance->user_id = Auth::user()->id;
        // $attendance->schedule_id = $request->input('schedule');
        // $attendance->save();

        // $schedule = Schedule::find($request->input('schedule'));
        // $schedule->isCurrent =  1;
        // $schedule->save();

        return $attendance;

    }

    public function signOut(Request $request, Schedule $schedule)
    {

        $signOutImg = file_put_contents(public_path('storage/attendance/') . $request->header('File-Name'), file_get_contents('php://input'));

        // get assigned attendance
        $attendance = Attendance::orderBy('id','desc')
                        ->where('user_id', Auth::user()->id)
                        ->where('schedule_id', $schedule->id)->first();

        // update attendance
        $attendance->sign_out_image = 'attendance/'. $request->header('File-Name');
        $attendance->sign_out = Carbon::now();
        $attendance->sign_out_latitude = $request->header('Latitude');
        $attendance->sign_out_longitude = $request->header('Longitude');
        $attendance->sign_out_speed = $request->header('Speed');
        $attendance->remarks = $request->header('Remarks');
        $attendance->isSync = 1;
        $attendance->save();

        // update schedule
        $schedule->isCurrent =  0;
        $schedule->status = 1;
        $schedule->save();

        return $attendance;
    }

    //User API

    public function getCurrentUser()
    {
        return ucfirst(strtolower(explode(' ',trim(Auth::user()->name))[0]));
    }

    // Sync API

    public function syncSignIn(Request $request)
    {

        file_put_contents(public_path('storage/attendance/') . $request->header('Sign-In-Image'), file_get_contents('php://input'));

        $attendance = new Attendance;
        $attendance->user_id = Auth::user()->id;
        $attendance->schedule_id = $request->header('Schedule-Id');
        $attendance->sign_in_image = 'attendance/'. $request->header('Sign-In-Image');
        $attendance->sign_in = $request->header('Sign-In');
        $attendance->sign_in_latitude = $request->header('Sign-In-Latitude');
        $attendance->sign_in_longitude = $request->header('Sign-In-Longitude');
        $attendance->sign_in_speed = $request->header('Sign-In-Speed');
        $attendance->save();

        $schedule = Schedule::find($request->header('Schedule-Id'));
        $schedule->isCurrent =  1;
        $schedule->save();

        return $attendance;

    }

    public function syncSignOut(Request $request, Schedule $schedule)
    {
        $signOutImg = file_put_contents(public_path('storage/attendance/') . $request->header('Sign-Out-Image'), file_get_contents('php://input'));

        // get assigned attendance
        $attendance = Attendance::orderBy('id','desc')
                        ->where('user_id', Auth::user()->id)
                        ->where('schedule_id', $schedule->id)->first();

        // update attendance
        $attendance->sign_out_image = 'attendance/'. $request->header('Sign-Out-Image');
        $attendance->sign_out = $request->header('Sign-Out');
        $attendance->sign_out_latitude = $request->header('Sign-Out-Latitude');
        $attendance->sign_out_longitude = $request->header('Sign-Out-Longitude');
        $attendance->sign_out_speed = $request->header('Sign-Out-Speed');
        $attendance->remarks = $request->header('Remarks');
        $attendance->isSync = 1;
        $attendance->save();

        // update schedule
        $schedule->isCurrent =  0;
        $schedule->status = 1;
        $schedule->save();

        return $attendance;
    }

    public function syncAttendances(Request $request)
    {

        file_put_contents(public_path('storage/attendance/') . $request->header('Sign-In-Image'), file_get_contents('php://input'));
        file_put_contents(public_path('storage/attendance/') . $request->header('Sign-Out-Image'), file_get_contents('php://input'));



        // Storage::putFile($request->header('Sign-In-Image'), new File(public_path('storage/attendance/')));
        // Storage::putFile($request->header('Sign-Out-Image'), new File(public_path('storage/attendance/')));

        $attendance = new Attendance;
        $attendance->user_id = Auth::user()->id;
        $attendance->schedule_id = $request->header('Schedule-Id');
        $attendance->sign_in_image = 'attendance/'. $request->header('Sign-In-Image');
        $attendance->sign_in = $request->header('Sign-In');
        $attendance->sign_in_latitude = $request->header('Sign-In-Latitude');
        $attendance->sign_in_longitude = $request->header('Sign-In-Longitude');
        $attendance->sign_in_speed = $request->header('Sign-In-Speed');
        $attendance->sign_out_image = 'attendance/'. $request->header('Sign-Out-Image');
        $attendance->sign_out = $request->header('Sign-Out');
        $attendance->sign_out_latitude = $request->header('Sign-Out-Latitude');
        $attendance->sign_out_longitude = $request->header('Sign-Out-Longitude');
        $attendance->sign_out_speed = $request->header('Sign-Out-Speed');
        $attendance->remarks = $request->header('Remarks');
        $attendance->isSync = 1;
        $attendance->save();

        $schedule = Schedule::find($request->header('Schedule-Id'));
        $schedule->isCurrent =  0;
        $schedule->status = 1;
        $schedule->save();

        return $attendance;

    }

    // Payments API

    public function getPayments()
    {
        // Fetch expenses from last week's start of the week up to current week's end week
        $payments = Expense::where('user_id',Auth::user()->id)
                        ->whereBetween('created_at', [Carbon::today()->subWeek()->startOfWeek()->toDateString(), Carbon::today()->endOfWeek()->toDateString()])
                        ->orderBy('id','DESC')
                        ->take(25)
                        ->get();

        return PaymentsResource::collection($payments);

    }


}
