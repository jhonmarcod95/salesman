<?php

namespace App\Console\Commands;

use App\User;
use DateTime;
use App\Expense;
use App\EmployeeWeeklyExpense;
use App\EmployeeMonthlyExpense;
use Illuminate\Console\Command;
use App\Services\ExpenseService;

class MonthlyVerificationCapture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'capture:monthly-verified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture all monthly Verified, Rejected, and Pendings per user.';

    /**
     * Initialization of Expense Service
     */
    private $expense_service, $last_week_date_range;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ExpenseService $expense_service)
    {
        parent::__construct();
        $this->expense_service = $expense_service;
        $this->last_week_date_range = $this->getLastWeekMonth();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = microtime(true);
        // $this->webexNotification(1021);
        // return;

        #==============================================================
        #TODAY is MONDAY

        #1. Check last weeks date.
        //If last's week month is same as today, run only this month
        //If last's week date is mixed, run last month's verified and last week's verified to current month

        #2. Run webex notification function to user
        //Loop month and get all rejected expense recorded for month
        #==============================================================
        // $last_month = date("Y-m", strtotime("first day of last month"));
        //Define last month
        $weekly_date_range = $this->expense_service->getWeekRangesOfMonthStartingMonday(date("Y-m"));

        //Define month and year
        $month_year = explode(' ', date('F Y'));
        $month = $month_year[0];
        $year = $month_year[1];

        $week_no = 1;
        foreach($weekly_date_range as $date_range) {
            $start_date = $date_range['start']. ' 00:00:01';
            $end_date = $date_range['end'] . ' 23:59:59';

            //Get all users with expenses for last month base on date range
            $user_with_expense = Expense::where('expenses_entry_id', '!=', 0)->whereBetween('created_at', [$start_date, $end_date])->distinct()->pluck('user_id');
            
            //Get user weekly verified stat and amount
            $userExpenses = User::select('id')->whereIn('id', $user_with_expense)
                ->has('expensesEntries')
                ->with(['expensesEntries' => function($query) use($start_date, $end_date){
                    $query->expensePerMonth($start_date, $end_date);
                }])
                ->get();

            foreach($userExpenses as $user) {
                $parameter = ['user_id' => $user->id, 'month' => $month, 'year' => $year];

                //Check if user with month and year exist in employee monthly expense
                $employee_monthly_expense_exists = EmployeeMonthlyExpense::where($parameter)->exists();
                if($employee_monthly_expense_exists) {
                    $employee_monthly_expense = EmployeeMonthlyExpense::where($parameter)->first();
                    
                    //If week 1, reset all data (need to reset the data to store new updated data)
                    if ($week_no == 1) {
                        $employee_monthly_expense->update([
                            'expense_count' => null,
                            'verified_count' => null,
                            'unverified_count' => null,
                            'rejected_count' => null,
                            'expense_amount' => null,
                            'verified_amount' => null,
                            'rejected_amount' => null,
                            'balance_rejected_amount' => null
                        ]);
                    }

                } else {
                    $employee_monthly_expense = EmployeeMonthlyExpense::create($parameter);
                }

                //Define expenses total variable per week
                $expenses_model_count   = 0;
                $verified_expense_count = 0;
                $unverified_expense_count = 0;
                $rejected_expense_count = 0;
                $total_expenses         = 0;
                $verified_amount        = 0;
                $rejected_amount        = 0;

                if (count($user->expensesEntries)) {
                    foreach ($user->expensesEntries as $expenses) {
                        //Compute expenses total
                        $expenses_model_count     = $expenses_model_count + $expenses->expenses_model_count;
                        $verified_expense_count   = $verified_expense_count + $expenses->verified_expense_count;
                        $unverified_expense_count = $unverified_expense_count + ($expenses->unverified_expense_count + $expenses->pending_expense_count);
                        $rejected_expense_count   = $rejected_expense_count + ($expenses->rejected_expense_count + $unverified_expense_count);
                        $total_expenses           = $total_expenses + $expenses->totalExpenses;

                        $verified = $this->expense_service->computeVerifiedAndRejected($expenses->expensesModel);
                        $total_expenses = $total_expenses + $verified['total_expense_amount'];
                        $verified_amount = $verified_amount + $verified['verified_amount'];
                        $rejected_amount = $rejected_amount + ($verified['total_expense_amount'] - $verified['verified_amount']);
                    }

                    //Prepare weekly verified data
                    $employeeWeeklyExpenseData = [
                        'employee_monthly_expense_id' => $employee_monthly_expense->id,
                        'week_no' => $week_no,
                        'user_id' => $user->id,
                        'expense_count' => count($user->expensesEntries) ? $expenses_model_count : 0,
                        'verified_count' => $verified_expense_count,
                        'unverified_count' => $unverified_expense_count,
                        'rejected_count' => $rejected_expense_count,
                        'expense_amount' => $total_expenses,
                        'verified_amount' => $verified_amount,
                        'rejected_amount' => $rejected_amount
                    ];

                    //Check if employee weekly expense exist
                    $isEmployeeWeeklyExpenseExist = EmployeeWeeklyExpense::where(['employee_monthly_expense_id' => $employee_monthly_expense->id, 'week_no' => $week_no])->exists();
                    if(!$isEmployeeWeeklyExpenseExist) {
                        // Store employee weekly expense
                        EmployeeWeeklyExpense::create($employeeWeeklyExpenseData);
                    } else {
                        // Update employee weekly expense
                        $employeeWeeklyExpense = EmployeeWeeklyExpense::where(['employee_monthly_expense_id' => $employee_monthly_expense->id, 'week_no' => $week_no])->first();
                        $employeeWeeklyExpense->update($employeeWeeklyExpenseData);
                    }

                    //Update employee monhtly expense
                    $employee_monthly_expense->update([
                        'expense_count' => $employee_monthly_expense->expense_count + (count($user->expensesEntries) ? $expenses_model_count : 0),
                        'verified_count' => $employee_monthly_expense->verified_count + $verified_expense_count,
                        'unverified_count' => $employee_monthly_expense->unverified_count + $unverified_expense_count,
                        'rejected_count' => $employee_monthly_expense->rejected_count + $rejected_expense_count,
                        'expense_amount' => $employee_monthly_expense->expense_amount + $total_expenses,
                        'verified_amount' => $employee_monthly_expense->verified_amount + $verified_amount,
                        'rejected_amount' => $employee_monthly_expense->rejected_amount + $rejected_amount,
                        'balance_rejected_amount' => $employee_monthly_expense->rejected_amount + $rejected_amount
                    ]);
                }
                $this->webexNotification($user->id, $week_no);
            }
            $week_no++;
        }

        $end_time = (microtime(true) - $start) / 60;
        echo "finished with $end_time time.";
    }

    public function webexNotification($user_id, $week_no) {
        // dd($this->last_week_date_range, $user_id, $week_no);
        foreach($this->last_week_date_range as $last_week) {
            //Define month and year
            $month_year = explode(' ', $last_week['month']);
            $month = $month_year[0];
            $year = $month_year[1];

            //Get last months rejected expense
            $employee_monthly_expense = EmployeeMonthlyExpense::where(['user_id' => $user_id, 'month' => $month, 'year' => $year])->first();
            
            if($employee_monthly_expense) {
                if($employee_monthly_expense->rejected_count > 0) {
                    // dd($employee_monthly_expense);

                    if ($last_week['type'] == 'present_month') {
                        $employee_weekly_expense = EmployeeWeeklyExpense::where(['employee_monthly_expense_id' => $employee_monthly_expense->id, 'week_no' => $week_no])->first();

                        $message = "<b>Last Week's Expense:</b>: <br> 
                                    October 13, 2024 - October 21, 2024
                                    Rejected count: 5 receipts
                                    Rejected amount: PHP 1,234.00   
                                    ";
                        dd($message);
                    }
                }
            }
        }
    }

    public function getLastWeekMonth() {
        // Get today's date
        $today = new DateTime();
        // $today = new DateTime("2024-09-16");

        // Find the most recent Sunday (which could be today)
        $lastSunday = clone $today;
        if ($today->format('N') != 7) {
            $lastSunday->modify('last sunday');
        }

        // Find the Sunday from the previous week
        $previousSunday = clone $lastSunday;
        $previousSunday->modify('-7 days');

        // Get week number forlast Sunday
        $lastSundayWeekOfMonth = $this->getWeekOfMonth($lastSunday);

        //Define date array
        $lastWeekDates = [];

        // Check if the date range spans two different months
        if ($previousSunday->format('m') !== $lastSunday->format('m')) {
            // Group by two months
            $startMonth = $previousSunday->format('F Y');
            $endMonth = $lastSunday->format('F Y');

            //Push month 1
            $dateRangeData = [];
            $dateRangeData['month'] = $startMonth;
            $dateRangeData['range']['start_date'] = $previousSunday->format('Y-m-01');
            $dateRangeData['range']['end_date'] = $previousSunday->format('Y-m-t 23:59:59');
            $dateRangeData['type'] = "last_month";
            $dateRangeData['week_no'] = null;
            $lastWeekDates[] = $dateRangeData;

            //Push month 2
            $dateRangeData = [];
            $dateRangeData['month'] = $endMonth;
            $dateRangeData['range']['start_date'] = $lastSunday->format('Y-m-01');
            $dateRangeData['range']['end_date'] = $lastSunday->format('Y-m-d 23:59:59');
            $dateRangeData['week_no'] = $lastSundayWeekOfMonth;
            $dateRangeData['type'] = "present_month";
            $lastWeekDates[] = $dateRangeData;
        } else {
            // Get month
            $month = $lastSunday->format('F Y');

            $week_no = ($lastSundayWeekOfMonth - 1) == 0 ? 1 : ($lastSundayWeekOfMonth - 1);

            //Push single month range
            $dateRangeData = [];
            $dateRangeData['month'] = $month;
            $dateRangeData['range']['start_date'] = $previousSunday->format('Y-m-d');
            $dateRangeData['range']['end_date'] = $lastSunday->format('Y-m-d 23:59:59');
            $dateRangeData['week_no'] = $week_no;
            $dateRangeData['type'] = "present_month";
            $lastWeekDates[] = $dateRangeData;
        }

        return $lastWeekDates;
    }

    // Function to calculate week number of the month
    public function getWeekOfMonth($date) {$firstDayOfMonth = clone $date;
        $firstDayOfMonth->modify('first day of this month');
        return intval(ceil(($date->format('d') + $firstDayOfMonth->format('N') - 1) / 7));
    }
}
