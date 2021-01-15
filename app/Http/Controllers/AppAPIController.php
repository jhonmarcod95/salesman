<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExpensesEntriesResult as ExpensesEntriesResult;
use App\Http\Resources\TinNumbersResource as TinNumbersResource;
use App\Http\Resources\SchedulesResource as SchedulesResource;
use App\Http\Resources\PaymentsResource as PaymentsResource;
use App\Http\Resources\APIExpenseResult as expenseResult;
use Illuminate\Support\Facades\Auth;
use App\SalesmanInternalOrder;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Http\Request;
use App\ExpenseChargeType;
use App\Rules\TinNumber;
use App\Rules\AmountLimit;
use App\ReceiptExpense;
use App\RequestSchedule;
use App\ExpensesEntry;
use App\ExpensesType;
use App\Attendance;
use Carbon\Carbon;
use App\Schedule;
use App\Expense;
use App\CloseVisit;
use App\Customer;
use App\Payment;
use App\ExpenseExclusive;
use App\ExpenseScheduleType;
use DB;


class AppAPIController extends Controller
{
    // Expenses App API
    /**
     * Get unverfied expenses within the current week
     *
     * @return json
     */
    public function getExpenses()  {

          $expenses = Expense::where('user_id',Auth::user()->id)
                        ->whereBetween('created_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
                        ->where('expenses_entry_id', 0)
                        ->get();
            return expenseResult::collection($expenses);

    }

    /**
     * Get all active expense types
     *
     * @return json
     */
    public function getExpensesType() {

        $filterIoCondition = "";
        // check first if included to expense schedule type for ristricting same expense type IOs
        $checkIfConditionIO = ExpenseScheduleType::where('salesman_id', Auth::user()->id)
        ->whereNotNull('default_expense_types')
        ->whereNotNull('expense_hide')
        ->whereNotNull('expense_display')
        ->whereNotNull('scheduletype_condition');

        // check current schedule
        $checkCurrentSchedule = Schedule::orderBy('id', 'DESC')
            ->whereDate('date', Carbon::today())
            ->where('user_id', Auth::user()->id)
            ->where('isCurrent', 1);

        if ($checkIfConditionIO->exists() == true) {
            // default to remove from schedule condition
            $filterIoCondition = json_decode($checkIfConditionIO->first()->default_expense_types, true);
        }

        if($checkIfConditionIO->exists() == true && $checkCurrentSchedule->exists() == true) {
               // check if has BGC schedule
            if ($checkCurrentSchedule->where('type', $checkIfConditionIO->first()->scheduletype_condition)->where('name', 'LIKE', '%BGC%')->exists() == true) { // if office is true and BGC ONLY
                $filterIoCondition = json_decode($checkIfConditionIO->first()->expense_hide, true); // normal parking will be remove
            } else {
                $filterIoCondition = json_decode($checkIfConditionIO->first()->expense_display, true); // default not to hide expense type;
            }
        }

        $expensesType = ExpensesType::whereStatus(1)
                    ->when($filterIoCondition, function ($query, $filterIoCondition) {
                        return $query->whereNotIn('id', $filterIoCondition);
                    })
                    ->get();

        return $expensesType;
    }

    /**
     * Check if IO is duplicate
     */
    public function checkDuplicateIo() {
        $internalOrders = SalesmanInternalOrder::where('user_id', Auth::user()->id)->get();

        $duplicateInternalOrders = collect($internalOrders)
            ->groupBy('internal_order')
            ->filter(function ($items) {
                return $items->count() > 1;
            })
            ->flatMap(function ($items) {
                return $items->map(function ($x) {
                    // Expense Charge Type
                    $expenseChargeType = ExpenseChargeType::where('charge_type_id', $x->chargeType->id);
                    return array(
                        'internal_order' => $x->internal_order,
                        'amount' => (float) $this->getUnprocessSubmittedExpense($expenseChargeType->first()->expenseType->id),
                    );
                });
            });

        return $duplicateInternalOrders;
    }

    /**
     * Check budget balance from SAP by given expense type and user id
     *
     * @param [Integer] $expense_type
     * @return void
     */
    public function checkBudget($expense_type)
    {
        if($this->checkInternalOrder($expense_type) != 'N/A') {
            if($this->checkInternalOrder($expense_type)) {

                $internalOrder = $this->checkInternalOrder($expense_type);

                $response = Curl::to('http://10.96.4.39/salesforcepaymentservice/api/sap_budget_checking')
                ->withContentType('application/x-www-form-urlencoded')
                ->withData(array( 'budget_line' => $internalOrder->internal_order, 'posting_date' => Carbon::today()->format('m/d/Y'), 'company_server'=> $internalOrder->sap_server ))
                ->post();

                $toJson = json_decode($response, true);

                // Expense Charge Type
                $expenseChargeType = ExpenseChargeType::where('charge_type_id', $internalOrder->chargeType->id);

                $simulatedBalancedReturn = (float) $toJson[0]['balance_amount'] - $this->getUnprocessSubmittedExpense($expenseChargeType->first()->expenseType->id);

                $zeroOrResult = $simulatedBalancedReturn < 0 ? 0 : $simulatedBalancedReturn;

                $isDuplicate = false;
                $isDuplicateIoAmount = 0;
                $isDuplicateInfo = $this->checkDuplicateIo()->where('internal_order', $internalOrder->internal_order);
                if ($isDuplicateInfo->count() > 1) {
                    $isDuplicate = true;
                    $isDuplicateIoAmount = (float) $toJson[0]['balance_amount'] - $isDuplicateInfo->sum('amount');
                }

                return $isDuplicate == true ? $isDuplicateIoAmount :  $zeroOrResult;

            }
            // if null, expense will not proceed
            return null;
        }
        // If N/A expense will proceed
        return 'N/A';
    }

    /**
     * Check the internal order record table from given expense type
     *
     * @param [Integer] $expense_type
     * @return void
     */
    public function checkInternalOrder($expense_type)
    {
        $isUserIo = SalesmanInternalOrder::where('user_id', Auth::user()->id);
        // // check if USER is found in SalesmanInternal Order
        if($isUserIo->exists()) {
            // when user found; check if expense type is exsisting
            $isUserHasChargeType = SalesmanInternalOrder::where('user_id', Auth::user()->id)->where('charge_type', $this->checkChargeType($expense_type))->first();
            if($isUserHasChargeType) {
                return $isUserHasChargeType;
            }
                return $isUserHasChargeType;
        }
        // users that excluded from SAP API
        return 'N/A';

    }

    /**
     * Check the charge type name from the table w/ given expense type
     *
     * @param [Integer] $expense_type
     * @return void
     */
    public function checkChargeType($expense_type)
    {
        $expenseChargeType = ExpenseChargeType::where('expense_type_id', $expense_type);
        if($expenseChargeType->exists()) {
            return $expenseChargeType->first()->chargeType->name;
        } else {
            return null;
        }
    }

    /**
     * Check all user's remaining balance in SAP api within the month
     *
     * @return void
     */
    public function checkUserBalance()
    {
        $internalOrders = SalesmanInternalOrder::where('user_id', Auth::user()->id)->get();

        $expenseBalances = array();

        $duplicateInternalOrders = collect($internalOrders)
                                ->groupBy('internal_order')
                                ->filter(function ($items) {
                                     return $items->count() > 1;
                                })
                                ->flatMap(function ($items) {
                                   return $items->map(function ($x) {
                                    // Expense Charge Type
                                    $expenseChargeType = ExpenseChargeType::where('charge_type_id', $x->chargeType->id);
                                        return array(
                                            'internal_order' => $x->internal_order,
                                            'amount' => (double) $this->getUnprocessSubmittedExpense($expenseChargeType->first()->expenseType->id),
                                        );
                                    });
                                });

        foreach($internalOrders as $internalOrder) {

            // SAP API
            $response = Curl::to('http://10.96.4.39/salesforcepaymentservice/api/sap_budget_checking')
            ->withContentType('application/x-www-form-urlencoded')
            ->withData(array( 'budget_line' => $internalOrder->internal_order, 'posting_date' => Carbon::today()->format('m/d/Y'), 'company_server'=> $internalOrder->sap_server ))
            ->post();

            $toJson = json_decode($response, true);

            // Expense Charge Type
            $expenseChargeType = ExpenseChargeType::where('charge_type_id', $internalOrder->chargeType->id);

            // Return value or zero when negative
            $simulatedBalancedReturn = (double) $toJson[0]['balance_amount'] - $this->getUnprocessSubmittedExpense($expenseChargeType->first()->expenseType->id);

            // Block for instances that theres a duplicate internal orders
            $isDuplicate = false;
            $isDuplicateIoAmount = 0;
            $isDuplicateInfo = $duplicateInternalOrders->where('internal_order', $internalOrder->internal_order);
            if($isDuplicateInfo->count() > 1) {
                $isDuplicate = true;
                $isDuplicateIoAmount = (float) $toJson[0]['balance_amount'] - $isDuplicateInfo->sum('amount');
            }

            $zeroOrResult = $simulatedBalancedReturn < 0 ? 0 : $simulatedBalancedReturn;

            //Calculate Ramaining total amound
            $totalBalance = $expenseChargeType->exists() ? $zeroOrResult : (double) $toJson[0]['balance_amount'];

            $data = array(
                'id' => $internalOrder->id,
                'charge_type' => $internalOrder->charge_type,
                'internal_order' => $internalOrder->internal_order,
                'expense_type' => $expenseChargeType->exists() ? $expenseChargeType->first()->expenseType->name : null,
                'expense_type_id' => $expenseChargeType->exists() ? $expenseChargeType->first()->expenseType->id : null,
                'sap_server' => $internalOrder->sap_server,
                'balance' => $isDuplicate == true ? $isDuplicateIoAmount : $totalBalance,
                'check_if_duplicate' => $isDuplicate,
            );
            array_push($expenseBalances,$data);

        }

        return $expenseBalances;
    }

    /**
     * Check all user's real remaining balance from SAP api
     *
     * @return void
     */
    public function checkUserRealBalance(Request $request)
    {

        $this->validate($request, [
            'user_id' => 'required'
        ]);

        $internalOrders = SalesmanInternalOrder::where('user_id', $request->user_id)->get();

        $expenseBalances = array();

        foreach($internalOrders as $internalOrder) {

            // SAP API
            $response = Curl::to('http://10.96.4.39/salesforcepaymentservice/api/sap_budget_checking')
            ->withContentType('application/x-www-form-urlencoded')
            ->withData(array( 'budget_line' => $internalOrder->internal_order, 'posting_date' => Carbon::today()->format('m/d/Y'), 'company_server'=> $internalOrder->sap_server ))
            ->post();

            $toJson = json_decode($response, true);

            // Expense Charge Type
            $expenseChargeType = ExpenseChargeType::where('charge_type_id', $internalOrder->chargeType->id);

            // Return value or zero when negative
            $simulatedBalancedReturn = (double) $toJson[0]['balance_amount'] - $this->getUnprocessSubmittedExpense($expenseChargeType->first()->expenseType->id);

            $zeroOrResult = $simulatedBalancedReturn < 0 ? 0 : $simulatedBalancedReturn;

            //Calculate Ramaining total amound
            // $totalBalance = $expenseChargeType->exists() ? $zeroOrResult : (double) $toJson[0]['balance_amount'];
            $totalBalance = (double) $toJson[0]['balance_amount'];

            $data = array(
                'id' => $internalOrder->id,
                'charge_type' => $internalOrder->charge_type,
                'internal_order' => $internalOrder->internal_order,
                'expense_type' => $expenseChargeType->exists() ? $expenseChargeType->first()->expenseType->name : null,
                'expense_type_id' => $expenseChargeType->exists() ? $expenseChargeType->first()->expenseType->id : null,
                'sap_server' => $internalOrder->sap_server,
                'balance' => $totalBalance
            );
            array_push($expenseBalances,$data);

        }

        return $expenseBalances;

    }

    public function getUnprocessSubmittedExpense($expenses_type_id)
    {
        $expense = Expense::whereUserId(Auth::user()->id)
                        ->where('expenses_type_id',$expenses_type_id)
                        ->whereBetween('created_at', [Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])
                        ->doesntHave('postedPayments')
                        // ->has('expensesEntry')
                        ->get();

        return $expense->sum('amount');
    }

    /**
     * get the total unpained spent per user in a month
     *
     * @return void
     */
    public function totalSpent()
    {
        $expense = Expense::whereUserId(Auth::user()->id)
                        ->whereBetween('created_at', [Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])
                        ->doesntHave('postedPayments')
                        ->has('expensesEntry')
                        ->get();

        return $expense->sum('amount');

    }

    /**
     * Check if user has expense restriction from
     * Model ExpenseExclusive
     */
    public function expenseRestriction($expense_type)
    {
        $check_expense_type = ExpenseExclusive::where('expense_exlusibable_type', 'App\ExpensesTypes')->where('expense_exlusivable_id', $expense_type);

        // the result should be false in order to allow the current user to claim this specific expense type
        if($check_expense_type->count() > 0) {
            $expnese_exclusive = collect(json_decode($check_expense_type->first()->users_array_id, true));
            return in_array(Auth::user()->id, $expnese_exclusive->toArray()) ? 'true' : 'false';
        }

        return false;

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
            'amount' => [new AmountLimit($request->input('types'), 0, $this->checkBudget($request->input('types'))), 'required'],
        ]);

        if($this->expenseRestriction($request->input('types')) == 'false') {
            $this->validate($request, [
                'expense_restriction' => 'required'
            ],[
                'expense_restriction.required' => 'Your account is not entitled to claim for this expense type'
            ]);
        }

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
            'amount' => [new AmountLimit($request->input('types'), $expense->id, $this->checkBudget($request->input('types'))), 'required'],
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
        $sweepExpense = Expense::where('user_id',Auth::user()->id)
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
                            ->orderBy('id','DESC')
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
                        ->whereBetween('date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
                        ->where('isCurrent', 1)
                        ->where('status',2)
                        ->with('closeVisit:id,schedule_id,isApproved')
                        ->first();


        return $currentSchedule;

        // return new SchedulesResource($currentSchedule);
    }

    public function getSchedules() {

        $schedules = Schedule::orderBy('date','ASC')
                        ->where('date', '>=', Carbon::today()->format('Y-m-d'))
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

    // for grassroots
    /**
     * Will check if current schedule
     * @return number of detected activity count
     */
    public function hasActivity()
    {

        $dailySchedule = Schedule::orderBy('id','DESC')
                            ->whereDate('date', Carbon::today())
                            ->where('user_id', Auth::user()->id)
                            ->whereIn('type',[6])
                            ->exists();

        return response()->json($dailySchedule);

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


    /**
     * function to force close a visit
     */
    public function closeVisit(Request $request, Schedule $schedule)
    {
        // update schedule
        $schedule->isCurrent =  0;
        $schedule->status = 1;
        $schedule->save();

        return $schedule;
    }

    public function requestToCloseVisit(Request $request)
    {
        $this->validate($request,[
            'schedule_id' => 'required',
            'reason' => 'required'
        ]);

        $closevisit = new CloseVisit();
        $closevisit->user_id = Auth::user()->id;
        $closevisit->reason = $request->reason;
        $closevisit->schedule()->associate($request->schedule_id);
        $closevisit->save();

        return $closevisit;
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
                        // ->take(25)
                        ->get();

        return PaymentsResource::collection($payments);

    }

    //Receipt Expenses API

    public function receiptExpense(ReceiptExpense $receiptExpense) {

        return $receiptExpense;

    }

    // Query receipt expenses
    public function checkTinNumber($tin_number) {
        $tin = ReceiptExpense::select('tin_number','vendor_name','vendor_address')
                            ->where('tin_number', $tin_number)
                            ->first();
        return $tin;
    }

    // Receipt Expenses API
    public function storeReceiptExpense(Request $request) {

        $this->validate($request, [
            'expense_id' => 'required',
            'receipt_transaction_id' => 'required', // VAT or NON-VAT
            'date_receipt' => 'required' // date of receipt
        ],[
            'receipt_transaction_id.required' => 'Please verify if VAT or NON-VAT',
        ]);

        if($request->input('receipt_transaction_id') == 1) {
                $this->validate($request, [
                    'receipt_type_id' => 'required', // Sales Invoice or Official Receipt
                    'vendor_name' => 'required',
                    'vendor_address' => 'required',
                    'tin_number' => [new TinNumber, 'required'],
                    // 'receipt_number' => 'required|unique:receipt_expenses', // OR# or SI#
                ],[
                    'receipt_type_id.required' => 'Please verify Receipt type'
                ]);
        }

        $findExpense = Expense::whereId($request->input('expense_id'))->whereNotIn('expenses_type_id',[1,3]);
        if($findExpense->exists()) {
            $this->validate($request,[
                'receipt_number' => 'required|unique:receipt_expenses',
            ]);
        }

        $existingTinNumber = $this->checkTinNumber($request->input('tin_number'));

        $receiptExpense = new ReceiptExpense;
        $receiptExpense->user_id = Auth::user()->id;
        $receiptExpense->expense()->associate($request->input('expense_id'));

        $receiptExpense->receipt_transaction_id = $request->input('receipt_transaction_id');
        $receiptExpense->receipt_type_id = $request->input('receipt_type_id') ?: 0;
        $receiptExpense->receipt_number = $request->input('receipt_number');
        $receiptExpense->date_receipt = $request->input('date_receipt');
        $receiptExpense->tin_number_extend = $request->input('tin_number_extend');

       if($existingTinNumber) {
            $receiptExpense->tin_number = $existingTinNumber->tin_number;
            $receiptExpense->vendor_name = $existingTinNumber->vendor_name;
            $receiptExpense->vendor_address = $existingTinNumber->vendor_address;
       } else {
           if($request->input('receipt_transaction_id') == 1) {
                $receiptExpense->tin_number = $request->input('tin_number');
                $receiptExpense->vendor_name = $request->input('vendor_name');
                $receiptExpense->vendor_address = $request->input('vendor_address');
           }
       }

        $receiptExpense->save();

        return $receiptExpense;

    }

    public function updateReceiptExpense(Request $request, ReceiptExpense $receiptExpense)
    {

        $findExpense = Expense::whereId($request->input('expense_id'))->whereNotIn('expenses_type_id',[1,3]);
        if($findExpense->exists()) {
            $this->validate($request,[
                'receipt_number' => 'required|unique:receipt_expenses,receipt_number,'.$receiptExpense->id, // OR# or SI#
            ]);
        }

        $this->validate($request, [
            'expense_id' => 'required',
            'receipt_transaction_id' => 'required', // VAT or NON-VAT
            'receipt_type_id' => 'required', // Sales Invoice or Official Receipt
            // 'receipt_number' => 'required|unique:receipt_expenses,receipt_number,'.$receiptExpense->id, // OR# or SI#
            'date_receipt' => 'required' // date of receipt
        ]);

        if($request->input('receipt_transaction_id') == 1) {
            $this->validate($request, [
                'vendor_name' => 'required',
                'vendor_address' => 'required',
                'tin_number' => [new TinNumber, 'required'],
            ]);
        }

        $existingTinNumber = $this->checkTinNumber($request->input('tin_number'));

        $receiptExpense->receipt_transaction_id = $request->input('receipt_transaction_id');
        $receiptExpense->receipt_type_id = $request->input('receipt_type_id');
        $receiptExpense->receipt_number = $request->input('receipt_number');
        $receiptExpense->date_receipt = $request->input('date_receipt');
        $receiptExpense->tin_number_extend = $request->input('tin_number_extend');

       if($existingTinNumber) {
            $receiptExpense->tin_number = $existingTinNumber->tin_number;
            $receiptExpense->vendor_name = $existingTinNumber->vendor_name;
            $receiptExpense->vendor_address = $existingTinNumber->vendor_address;
       } else {
           if($request->input('receipt_transaction_id') == 1) {
                $receiptExpense->tin_number = $request->input('tin_number');
                $receiptExpense->vendor_name = $request->input('vendor_name');
                $receiptExpense->vendor_address = $request->input('vendor_address');
           }
       }

        $receiptExpense->save();

        return $receiptExpense;

    }

    public function getTinNumbers() {

        $tinNumbers = ReceiptExpense::where('receipt_transaction_id',1)
                            ->orderBy('id','DESC')
                            ->get()
                            ->unique('tin_number');

        return TinNumbersResource::collection($tinNumbers);

    }

    public function checkUserRole()
    {
        if(Auth::user()->hasRole(['admin','id','approver','manager','vp','president']))
        {
            return response()->json(true,200);
        }
            return response()->json(false,200);
    }

    // get customer id based from customer_code

    public function getCustomer($customer_code)
    {
        $customer = Customer::where('customer_code', $customer_code)->first();

        return $customer;
    }


}
