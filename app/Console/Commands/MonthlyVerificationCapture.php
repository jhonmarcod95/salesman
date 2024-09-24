<?php

namespace App\Console\Commands;

use App\Expense;
use App\Services\ExpenseService;
use App\User;
use Illuminate\Console\Command;

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
        $last_month = date("Y-m", strtotime("first day of last month"));
        $weekly_date_range = $this->expense_service->getWeekRangesOfMonthStartingMonday($last_month);

        foreach($weekly_date_range as $date_range) {
            $start_date = $date_range['start']. ' 00:00:01';
            $end_date = $date_range['end'] . ' 23:59:59';

            //Get all users with expenses for last month base on date range
            $user_with_expense = Expense::where('expenses_entry_id', '!=', 0)->whereBetween('created_at', [$start_date, $end_date])->distinct()->pluck('user_id');
            
            //Get user weekly verified stat and amount
            $userExpenses = User::select('id')->whereIn('id', $user_with_expense)
                ->with(['expensesEntries' => function($query) use($start_date, $end_date){
                    $query->expensePerMonth($start_date, $end_date);
                }])
                ->get();

            dd($userExpenses[0]);
        }

    }
}
