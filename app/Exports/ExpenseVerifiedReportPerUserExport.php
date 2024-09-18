<?php

namespace App\Exports;

use App\Company;
use App\ExpensesEntry;
use App\Http\Controllers\UserController;
use App\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;

class ExpenseVerifiedReportPerUserExport implements FromCollection, WithHeadings {

    private $month_weekly_date_range; 
    private $user_id; 
    private $company_id;

    private $header = [
        'NAME',
        'BU',
    ];

    public function __construct($request, $month_weekly_date_range)
    {
        $this->month_weekly_date_range = $month_weekly_date_range;
        $this->user_id = $request->user_id;
        $this->company_id = $request->company;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect($this->getData());
    }

    public function headings(): array{
        $heading = $this->header;
        $week_count = 0;

        //Setup week header
        foreach($this->month_weekly_date_range as $week) {
            $week_count++;
            $heading[] = "WEEK $week_count";
        }

        return $heading;
    }

    public function getData()
    {
        $users = [];
        $user_data = [];

        if(isset($this->user_id)) {
            //Get specific user if user id is given
            $users = User::select('id','name')->where('id', $this->user_id)->with('companies')->get();
        } else {
            //Get all users base on given company
            $users = (new UserController())->getUsersWithExpense($this->company_id);
            $company = isset($this->company_id) ? (Company::find($this->company_id, ['id', 'name'])) : '';
        }

        //Get user details name and company.
        foreach ($users as $user) {
            $data = [];
            $data[] = $user->name;
            $data[] = isset($this->company_id) ? $company->name : $user->companies[0]->name;
            $user_data[] = $data;
        }
        
        //Get user ids
        $user_ids = $users->pluck('id')->toArray();
        
        //Set column where week will be placed
        $week_column_position = 3 /**column C */;

        //Loop through weekly date range
        foreach ($this->month_weekly_date_range as $week) {
            //Set start and end date with time
            $start_date = $week['start'] . " 00:00:01";
            $end_date = $week['end'] . " 23:59:59";

            //Filter user with expense entries base on user ids
            $user_expense = User::select('id', 'name')
                ->whereIn('id', $user_ids)
                ->with(['expensesEntries' => function ($q) use ($start_date, $end_date) {
                    $q->expensePerMonth($start_date, $end_date);
                }])
                ->orderBy('name', 'ASC')
                ->get();

            //Loop through users to get expense details status
            foreach($user_expense as $key => $user) {

                $expense_model_count = 0;
                $expense_count = 0;
                $verified = 0;
                $unverified = 0;
                $rejected = 0;

                foreach ($user->expensesEntries as $expense) {
                    //Define count of each expenses and its status
                    $expense_count = $expense_count + $expense->expenses_model_count;
                    $verified = $verified + $expense->verified_expense_count;
                    $unverified = $unverified + ($expense->unverified_expense_count + $expense->pending_expense_count);
                    $rejected = $rejected + $expense->rejected_expense_count;
                }

                // Set Default value of remark
                $remark = "No Reimbursement";

                //Match weekly expense status/remarks base on condition expense status
                if($expense_count) {
                    switch (true) {
                        case $expense_count == $verified:
                            $remark = "Verified";
                            break;
                        case $expense_count == $unverified:
                            $remark = "Unverified";
                            break;
                        case $expense_count == $rejected:
                            $remark = "Rejected";
                            break;
                        case $expense_count > $unverified:
                            $remark = "Incomplete";
                            break;
                    }
                }

                //Assign remarks to user and its weeks column
                $user_data[$key][$week_column_position] = $remark;
            }

            //Increment week column position
            $week_column_position++;
        }

        //Return user data
        return $user_data;
    }
}
