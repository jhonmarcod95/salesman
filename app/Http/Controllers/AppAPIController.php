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
use App\Payment;
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
     * Check budget balance from SAP by given expense type and user id
     *
     * @param [Integer] $expense_type
     * @return void
     */
    public function checkBudget($expense_type)
    {
        if($this->checkInternalOrder($expense_type)) {

            $internalOrder = $this->checkInternalOrder($expense_type);

            $response = Curl::to('http://10.96.4.39/salesforcepaymentservice/api/sap_budget_checking')
            ->withContentType('application/x-www-form-urlencoded')
            ->withData(array( 'budget_line' => $internalOrder->internal_order, 'posting_date' => Carbon::today()->format('m/d/Y'), 'company_server'=> $internalOrder->sap_server ))
            ->post();

            $toJson = json_decode($response, true);

            return $toJson[0]['balance_amount'];

        }
        return null;
    }

    /**
     * Check the internal order record table from given expense type
     *
     * @param [Integer] $expense_type
     * @return void
     */
    public function checkInternalOrder($expense_type)
    {
        // can be return as null
        // get the internal_order & server
        return SalesmanInternalOrder::where('user_id', Auth::user()->id)
                                    ->where('charge_type', $this->checkChargeType($expense_type))
                                    ->first();
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
                        ->where('isCurrent', 1)
                        ->first();

        return $currentSchedule;
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
                    'receipt_number' => 'required|unique:receipt_expenses', // OR# or SI#
                ],[
                    'receipt_type_id.required' => 'Please verify Receipt type'
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
        $this->validate($request, [
            'expense_id' => 'required',
            'receipt_transaction_id' => 'required', // VAT or NON-VAT
            'receipt_type_id' => 'required', // Sales Invoice or Official Receipt
            'receipt_number' => 'required|unique:receipt_expenses,receipt_number,'.$receiptExpense->id, // OR# or SI#
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


}
