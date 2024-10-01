<?php

namespace App\Exports;

use App\User;
use App\Company;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseController;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Http\Controllers\CoordinatorReportController;
use Doctrine\DBAL\Tools\Dumper;

class CoordinatorValidatedReportExport implements FromCollection, WithHeadings
{
    private $request, $company_id, $month_weekly_date_range;
    private $header = [
        'COORDINATOR',
        'BU',
    ];
    public function __construct($request)
    {
        $this->request = $request;
        $this->month_weekly_date_range = (new ExpenseController())->getWeekRangesOfMonthStartingMonday($request->month_year);
        $this->company_id = $request->company;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->getData());
    }

    public function headings(): array
    {
        $heading = $this->header;
        $week_count = 0;

        //Setup week header
        foreach ($this->month_weekly_date_range as $week) {
            $week_count++;
            $heading[] = "WEEK $week_count";
        }

        return $heading;
    }

    public function getData() {
        $user_data = [];
        $users = (new UserController())->selectionCoordinators($this->company_id);
        $company = isset($this->company_id) ? (Company::find($this->company_id, ['id', 'name'])) : '';

        //Get user details name and company.
        foreach ($users as $user) {
            $data = [];
            $data[] = $user['name'];
            $data[] = isset($this->company_id) ? $company->name : $user['company'];
            $user_data[] = $data;
        }
        // return $user_data;

        //Get user ids
        $user_ids = $users->pluck('id')->toArray();

        //Set column where week will be placed
        $week_column_position = 3/**column C */;

        //Loop through weekly date range
        foreach ($this->month_weekly_date_range as $week) {

            //Set start and end date with time
            $start_date = $week['start'] . " 00:00:01";
            $end_date = $week['end'] . " 23:59:59";

            $userValidatedExpenses = User::select('id', 'name')->whereIn('id', $user_ids)->coordinatorValidatedExpense($start_date, $end_date, $this->company_id)->orderBy('name', 'ASC')->get();
            
            foreach($userValidatedExpenses as $key => $expense) {

                //Assign validated count to user and its weeks column
                $user_data[$key][$week_column_position] = $expense->validated_expenses_count ?: '0';
            }

            //Increment week column position
            $week_column_position++;
        }

        //Return user data
        return $user_data;
    }
}
