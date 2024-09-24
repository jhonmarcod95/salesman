<?php

namespace App\Console\Commands;

use App\User;
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
    protected $signature = 'monthly:verification-capture';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture all Verified, Rejected, and Pendings per month per user.';

    /**
     * Initialization of Expense Service
     */
    private $expense_service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ExpenseService $expense_service)
    {
        parent::__construct();
        $this->expense_service = $expense_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Define last month
        $last_month = date("Y-m", strtotime("first day of last month"));
        $weekly_date_range = $this->expense_service->getWeekRangesOfMonthStartingMonday($last_month);

        //Define month and year
        $month_year = explode(' ', date('F Y', strtotime("last month")));
        $month = $month_year[0];
        $year = $month_year[1];

        foreach($weekly_date_range as $date_range) {
            $start_date = $date_range['start']. ' 00:00:01';
            $end_date = $date_range['end'] . ' 23:59:59';

            //Get all users with expenses for last month base on date range
            $user_with_expense = Expense::where('expenses_entry_id', '!=', 0)->whereBetween('created_at', [$start_date, $end_date])->distinct()->pluck('user_id');
            
            //Get user weekly verified stat and amount
            $userExpenses = User::select('id')->whereIn('id', $user_with_expense)
                ->with('expensesEntries')
                ->with(['expensesEntries' => function($query) use($start_date, $end_date){
                    $query->expensePerMonth($start_date, $end_date);
                }])
                ->limit(10)
                ->get();

            $week_no = 1;
            foreach($userExpenses as $user) {
                //Check if user with month and year exist in employee monthly expense
                $employee_monthly_expense_exists = EmployeeMonthlyExpense::where(['user_id' => $user->id, 'month' => $month, 'year' => $year])->exists();
                if($employee_monthly_expense_exists) {
                    $employee_monthly_expense = EmployeeMonthlyExpense::where(['user_id' => $user->id, 'month' => $month, 'year' => $year])->first();
                } else {
                    $employee_monthly_expense = EmployeeMonthlyExpense::create(['user_id' => $user->id, 'month' => $month, 'year' => $year]);
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
                        $rejected_expense_count   = $rejected_expense_count + $expenses->rejected_expense_count;
                        $total_expenses           = $total_expenses + $expenses->totalExpenses;

                        $verified = $this->expense_service->computeVerifiedAndRejected($expenses->expensesModel);
                        $verified_amount = $verified_amount + $verified['verified_amount'];
                        $rejected_amount = $rejected_amount + $verified['rejected_amount'];
                    }

                    // Store employee weekly expense
                    EmployeeWeeklyExpense::create([
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
                    ]);

                    //Update employee monhtly expense
                    $employee_monthly_expense->update([
                        'expense_count' => $employee_monthly_expense->expense_count + count($user->expensesEntries) ? $expenses_model_count : 0,
                        'verified_count' => $employee_monthly_expense->verified_count + $verified_expense_count,
                        'unverified_count' => $employee_monthly_expense->unverified_count + $unverified_expense_count,
                        'rejected_count' => $employee_monthly_expense->rejected_count + $rejected_expense_count,
                        'expense_amount' => $employee_monthly_expense->expense_amount + $total_expenses,
                        'verified_amount' => $employee_monthly_expense->verified_amount + $verified_amount,
                        'rejected_amount' => $employee_monthly_expense->rejected_amount + $rejected_amount
                    ]);
                }
            }
            $week_no++;
        }

    }
}
