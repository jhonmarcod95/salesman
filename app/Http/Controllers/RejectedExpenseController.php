<?php

namespace App\Http\Controllers;

use DateTime;
use App\Expense;
use Illuminate\Http\Request;
use App\EmployeeMonthlyExpense;
use Illuminate\Support\Facades\Auth;

class RejectedExpenseController extends Controller
{
    public function index() {
        return view('expense.index-rejected-report');
    }

    public function all(Request $request) {
        $dateMonthYear = new DateTime($request->month_year);
        $month = $dateMonthYear->format('F');
        $year = $dateMonthYear->format('Y');

        $company = $request->company;
        $monthly_validated = EmployeeMonthlyExpense::with(
                'user:id,name,company_id',
                'user.company:id,code,name'
            )
            // ->where(['month' => $month, 'year' => $year])
            ->where('user_id', Auth::user()->id)
            ->where(function($query) {
                $query->where('rejected_count', '>', 0)
                    ->orWhere('unverified_count', '>', 0);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->limit);

        $monthly_validated->getCollection()->transform(function($item) {
            $data = [];
            $data['month_year'] = $item->month." ". $item->year;
            $data['id'] = $item->user->id;
            $data['name'] = $item->user->name;
            $data['company'] = isset($item->user->company) ? $item->user->company->name : '';
            $data['receipt_count'] = $item->expense_count;
            $data['unverified_count'] = $item->unverified_count;
            $data['unverified_amount'] = $item->unverified_amount;
            $data['rejected_count'] = $item->rejected_count;
            $data['rejected_amount'] = $item->rejected_amount;
            return $data;
        });

        return $monthly_validated;
    }

    public function show(Request $request) {
        $firstDay = (new DateTime("first day of $request->month_year"))->format('Y-m-d');
        $lastDay = (new DateTime("last day of $request->month_year"))->format('Y-m-d 23:59:59');

        return Expense::with(
            'expensesType',
            'payments',
            'expenseVerificationStatus:id,name',
            'expenseRejectedRemarks:id,remark',
            'representaion:id,expense_id,attendees,purpose',
            'verifier:id,name',
            'routeTransportation:id,expense_id,from,to,transportation_id,remarks',
            'routeTransportation.transportation:id,mode',
            'grassroots:id,grassroots_expense_type_id,expense_id,remarks',
            'grassroots.grassrootExpenseType:id,name')
            ->whereBetween('created_at', [$firstDay, $lastDay])
            ->where('user_id', $request->user_id)
            ->where('expenses_entry_id', '!=', 0)
            ->where(function($query) {
                $query
                ->where('verified_status_id', 3 /**Rejected */)
                ->orWhereIn('verified_status_id', [0,2] /**Unverirfied */);
            })
            ->get();
    }
}
