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
    private $expense_service, $last_week_date_range, $month_weekly_date_range, $is_ninth_of_temonth, $date_today, $date_last_month;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ExpenseService $expense_service)
    {
        parent::__construct();
        $this->expense_service = $expense_service;
        $this->month_weekly_date_range = $this->expense_service->getWeekRangesOfMonthStartingMonday(date("Y-m"));
        $this->last_week_date_range = $this->getLastWeekMonth();

        $this->date_today = date('F Y');
        $this->date_last_month = date("F Y", strtotime("first day of last month"));

        $this->is_ninth_of_temonth = (now()->format('d') == '09') ? true : false;
        // $this->is_ninth_of_temonth = true;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // return;
        $start = microtime(true);

        //Define month and year
        $month_year = explode(' ', date('F Y'));
        $month = $month_year[0];
        $year = $month_year[1];

        //Get monthly verified entry ids
        $monthlyExpenseIds = EmployeeMonthlyExpense::where(['month' => $month, 'year' => $year])->get(['id'])->pluck('id');
        //Delete existing weekly verified data with month and year
        EmployeeWeeklyExpense::whereIn('employee_monthly_expense_id', $monthlyExpenseIds)->forceDelete();
        //Delete existing monthly verified data with month and year
        EmployeeMonthlyExpense::where(['month' => $month, 'year' => $year])->forceDelete();

        $week_no = 1;
        foreach($this->month_weekly_date_range as $date_range) {
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
                $unverified_amount      = 0;
                $rejected_amount        = 0;

                if (count($user->expensesEntries)) {
                    foreach ($user->expensesEntries as $expenses) {
                        //Compute expenses total
                        $expenses_model_count     = $expenses_model_count + $expenses->expenses_model_count;
                        $verified_expense_count   = $verified_expense_count + $expenses->verified_expense_count;
                        $unverified_expense_count = $unverified_expense_count + ($expenses->unverified_expense_count + $expenses->pending_expense_count);
                        $rejected_expense_count   = $rejected_expense_count + $expenses->rejected_expense_count;

                        $verified = $this->expense_service->computeVerifiedAndRejected($expenses->expensesModel);
                        $total_expenses = $total_expenses + $verified['total_expense_amount'];
                        $verified_amount = $verified_amount + $verified['verified_amount'];
                        $unverified_amount = $unverified_amount + $verified['unverified_amount'];
                        $rejected_amount = $rejected_amount + $verified['rejected_amount'];
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
                        'expense_amount' => (double) $total_expenses,
                        'verified_amount' => (double) $verified_amount,
                        'unverified_amount' => (double) $unverified_amount,
                        'rejected_amount' => (double) $rejected_amount
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
                        'verified_amount' => (double) $employee_monthly_expense->verified_amount + $verified_amount,
                        'unverified_amount' => (double) $employee_monthly_expense->unverified_amount + $unverified_amount,
                        'rejected_amount' => (double) $employee_monthly_expense->rejected_amount + $rejected_amount,
                        'balance_rejected_amount' => (double) $employee_monthly_expense->rejected_amount + ($unverified_amount + $rejected_amount)
                    ]);
                }
            }
            $week_no++;
        }

        //Send Webex notification
        $this->webexNotification();

        $end_time = (microtime(true) - $start) / 60;
        echo "finished with $end_time time.";
    }

    public function webexNotification() {
        //Define month and year
        $month_year = explode(' ', $this->date_today);
        $month = $month_year[0];
        $year = $month_year[1];

        //Get monthly verified entry user ids
        $userMonthlyExpenseIds = EmployeeMonthlyExpense::where(['month' => $month, 'year' => $year])->get(['user_id'])->pluck('user_id');

        foreach($userMonthlyExpenseIds as $user_id) {
            //Define webex card array
            $monthlyRejectedExpensesCard = [];
            $lastWeekRejectedExpensesCard = [];

            if ($this->is_ninth_of_temonth) { 
                #Send Only last month summary if today is 9th day of the month
                //Define month and year
                $month_year = explode(' ', $this->date_last_month);
                $month = $month_year[0];
                $year = $month_year[1];
                
                //Get verification summary from last month's expenses
                $employee_monthly_expense = EmployeeMonthlyExpense::where(['user_id' => $user_id, 'month' => $month, 'year' => $year])->first();

                //Assign monthly rejected card
                if ($employee_monthly_expense->rejected_count > 0 || $employee_monthly_expense->unverified_count > 0) {
                    $monthlyRejectedExpensesCard = $this->formatMonthlyCard($employee_monthly_expense, $this->date_last_month);
                } else {
                    continue;
                }
            } else {
                //Loop last week date range
                foreach($this->last_week_date_range as $last_week) {
                    // dd($this->last_week_date_range);
                    //Define month and year
                    $month_year = explode(' ', $last_week['month']);
                    $month = $month_year[0];
                    $year = $month_year[1];

                    //Get last months rejected expense
                    $employee_monthly_expense = EmployeeMonthlyExpense::where(['user_id' => $user_id, 'month' => $month, 'year' => $year])->first();
                    
                    if($employee_monthly_expense) {
                        if($employee_monthly_expense->rejected_count > 0 || $employee_monthly_expense->unverified_count > 0) {

                            //Set MOnthly rejected Card ===============
                            $monthlyRejectedExpensesCard = array_merge($monthlyRejectedExpensesCard, $this->formatMonthlyCard($employee_monthly_expense, $last_week['month']));

                            if ($last_week['type'] == 'present_month') {

                                $employee_weekly_expense = EmployeeWeeklyExpense::where(['employee_monthly_expense_id' => $employee_monthly_expense->id, 'week_no' => $last_week['week_no']])->first();

                                if($employee_weekly_expense) {
                                    $range = date('F d Y', strtotime($last_week['range']['start'])) . " - " . date('F d Y', strtotime($last_week['range']['end']));
                                    $rejected_count = $employee_weekly_expense->rejected_count;
                                    $rejected_amount = number_format($employee_weekly_expense->rejected_amount, 2);
                                    $unverified_count = $employee_weekly_expense->unverified_count;
                                    $unverified_amount = number_format($employee_weekly_expense->unverified_amount, 2);

                                    //Set Weekly rejected Card ===============
                                    $lastWeekRejectedExpensesCard[] = [
                                        "type" => "TextBlock",
                                        "text" => "Last Week's Expense", //MONTH
                                        "size" => "Default",
                                        "weight" => "Bolder",
                                        "wrap" => true,
                                        "spacing" => "Large",
                                        "horizontalAlignment" => "Left",
                                        "maxLines" => 5
                                    ];
                                    $lastWeekRejectedExpensesCard[] = [
                                        "type" => "TextBlock",
                                        "text" => "$range", //Last Week Date Range
                                        "size" => "Default",
                                        "wrap" => true,
                                        "spacing" => "None"
                                    ];
                                    $lastWeekRejectedExpensesCard[] = [
                                        "type" => "TextBlock",
                                        "text" => "Unverified count: $unverified_count receipts", //Unverified Count
                                        "size" => "Default",
                                        "wrap" => true,
                                        "spacing" => "None"
                                    ];
                                    $lastWeekRejectedExpensesCard[] = [
                                        "type" => "TextBlock",
                                        "text" => "Unverified amount: PHP $unverified_amount", //Unverified Amount
                                        "size" => "Default",
                                        "wrap" => true,
                                        "spacing" => "None"
                                    ];
                                    $lastWeekRejectedExpensesCard[] = [
                                        "type" => "TextBlock",
                                        "text" => "Rejected count: $rejected_count receipts", //Rejected Count
                                        "size" => "Default",
                                        "wrap" => true,
                                        "spacing" => "None"
                                    ];
                                    $lastWeekRejectedExpensesCard[] = [
                                        "type" => "TextBlock",
                                        "text" => "Rejected amount: PHP $rejected_amount", //Rejected Amount
                                        "size" => "Default",
                                        "wrap" => true,
                                        "spacing" => "None"
                                    ];
                                }
                            }
                        } else {
                            continue;
                        }
                    }
                }
            }

            //Card header ===============
            $user_name = (User::find($user_id))->name;
            $cardHeader = [
                [
                    "type" => "TextBlock",
                    "text" => "SALESFORCE APP ($user_name)",
                    "size" => "Default",
                    "color" => "Light"
                ],
                [
                    "type" => "TextBlock",
                    "text" => "REJECTED EXPENSES",
                    "color" => "Attention",
                    "weight" => "Bolder",
                    "size" => "ExtraLarge",
                    "spacing" => "None"
                ],
                [
                    "type" => "TextBlock",
                    "text" => "As of October 10, 2024 5:05PM",
                    "isSubtle" => true,
                    "spacing" => "None",
                    "size" => "Small"
                ],
                [
                    "type" => "Container",
                    "spacing" => "Default",
                    "separator" => true
                ],
            ];

            //Deduction Notice ============
            $noticeInfoCard = [];
            if ($this->is_ninth_of_temonth) {
                $noticeInfoCard = [
                    [
                        "type" => "TextBlock",
                        "text" => "IMPORTANT: All rejected and unverified expenses will be subjected to ATD. Changes can be made if the coordinator validate expense until tomorrow.", //MONTH
                        "size" => "Default",
                        "weight" => "Default",
                        "wrap" => true,
                        "spacing" => "Large",
                        "horizontalAlignment" => "Left",
                        "maxLines" => 5
                    ],
                ];
            }
            //Merge all extracted data
            $rejected_data_card = array_merge($monthlyRejectedExpensesCard, $lastWeekRejectedExpensesCard, $noticeInfoCard);

            if(!empty($rejected_data_card)) {
                //Merge all cards
                $cardItems = array_merge($cardHeader, $rejected_data_card);
                
                $webexCard = [
                    array(
                        "contentType" => "application/vnd.microsoft.card.adaptive",
                        "content" =>  [
                            "type" => "AdaptiveCard",
                            "body" => [
                                [
                                    "type" => "ColumnSet",
                                    "columns" => [
                                        [
                                            "type" => "Column",
                                            "width" => 2,
                                            "items" => $cardItems
                                        ]
                                    ]
                                ]
                            ],
                            '$schema' => "http://adaptivecards.io/schemas/adaptive-card.json",
                            "version" => "1.2"
                        ]
                    )
                ];

                //Send Webex Notif
                ExpenseService::sendSingleWebexNotif('demetrio.viray@lafilgroup.com', $webexCard);
            }
        }
    }

    public function getLastWeekMonth() {
        // Get today's date
        $today = new DateTime();

        // Find the most recent Saturday
        $lastSaturday = clone $today;
        if ($today->format('N') != 6) {
            $lastSaturday->modify('last saturday');
        }

        // Find the Sunday before the last Saturday
        $lastSunday = clone $lastSaturday;
        $lastSunday->modify('last sunday');

        //Define date array
        $lastWeekDates = [];

        // Check if the date range spans two different months
        if ($lastSunday->format('m') !== $lastSaturday->format('m')) {
            // Group by two months
            $startMonth = $lastSunday->format('F Y');
            $endMonth = $lastSaturday->format('F Y');
            $startOfNextMonth = clone $lastSaturday;
            $startOfNextMonth->modify('first day of this month');

            //Push month 1
            $dateRangeData = [];
            $dateRangeData['month'] = $startMonth;
            $dateRangeData['range']['start'] = $lastSunday->format('Y-m-01');
            $dateRangeData['range']['end'] = $lastSunday->format('Y-m-t');
            $dateRangeData['type'] = "last_month";
            $dateRangeData['week_no'] = null;
            $lastWeekDates[] = $dateRangeData;


            //Push month 2
            $dateRangeData = [];
            $dateRangeData['month'] = $endMonth;
            $dateRangeData['range']['start'] = $startOfNextMonth->format('Y-m-d');
            $dateRangeData['range']['end'] = $lastSaturday->format('Y-m-d');
            $dateRangeData['week_no'] = 1;
            $dateRangeData['type'] = "present_month";
            $lastWeekDates[] = $dateRangeData;
        } else {
            // Single month range
            $month = $lastSunday->format('F Y');

            //Push single month range
            $dateRangeData = [];
            $dateRangeData['month'] = $month;
            $dateRangeData['range']['start'] = $lastSunday->format('Y-m-d');
            $dateRangeData['range']['end'] = $lastSaturday->format('Y-m-d');
            $dateRangeData['week_no'] = $this->getWeekNo($this->month_weekly_date_range, $lastSunday->format('Y-m-d'), $lastSaturday->format('Y-m-d'));
            $dateRangeData['type'] = "present_month";
            $lastWeekDates[] = $dateRangeData;
        }
        
        return $lastWeekDates;

    }

    // Function to calculate week number of the month based on date
    public function getWeekOfMonth($date) {
        $firstDayOfMonth = clone $date;
        $firstDayOfMonth->modify('first day of this month');
        return intval(ceil(($date->format('d') + $firstDayOfMonth->format('N') - 1) / 7));
    }

    public function getWeekNo($monthRange, $start, $end) {
        $week_no = null;
        foreach ($monthRange as $key => $range) {
            if ($range['start'] === $start && $range['end'] === $end) {
                $week_no = $key + 1;
                break; // Stop after finding the first match
            }
        }
        return $week_no;
    }

    public function formatMonthlyCard($data, $month_year) {
        $expense_month = $month_year;
        $rejected_count = $data->rejected_count;
        $rejected_amount = number_format($data->rejected_amount, 2);
        $unverified_count = $data->unverified_count;
        $unverified_amount = number_format($data->unverified_amount, 2);
        //Set MOnthly rejected Card ===============
        $monthlyRejectedExpensesCard = [];
        $monthlyRejectedExpensesCard[] = [
            "type" => "TextBlock",
            "text" => $expense_month, //MONTH
            "size" => "Default",
            "weight" => "Bolder",
            "wrap" => true,
            "spacing" => "Large",
            "horizontalAlignment" => "Left",
            "maxLines" => 5
        ];
        $monthlyRejectedExpensesCard[] = [
            "type" => "TextBlock",
            "text" => "Unverified count: $unverified_count receipts", //Unverified Count
            "size" => "Default",
            "wrap" => true,
            "spacing" => "None"
        ];
        $monthlyRejectedExpensesCard[] = [
            "type" => "TextBlock",
            "text" => "Unverified amount: PHP $unverified_amount", //Unverified Amount
            "size" => "Default",
            "wrap" => true,
            "spacing" => "None"
        ];
        $monthlyRejectedExpensesCard[] = [
            "type" => "TextBlock",
            "text" => "Rejected count: $rejected_count receipts", //Rejected Count
            "size" => "Default",
            "wrap" => true,
            "spacing" => "None"
        ];
        $monthlyRejectedExpensesCard[] = [
            "type" => "TextBlock",
            "text" => "Rejected amount: PHP $rejected_amount", //Rejected Amount
            "size" => "Default",
            "wrap" => true,
            "spacing" => "None"
        ];

        return $monthlyRejectedExpensesCard;
    }
}
