<?php

namespace App\Http\Controllers;

use App\User;
use App\Expense;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CoordinatorValidatedReportExport;

class CoordinatorReportController extends Controller
{
    public function index() {
        return view('expense.coordinator-report');
    }

    /**
     * Handle common query for expense per user
     *
     */
    public function expensePerUserCommonQuery($request)
    {
        $start_date = "$request->start_date 00:00:01";
        $end_date = "$request->end_date 23:59:59";
        $company_id = $request->company;
        $coordinator_id = $request->coordinator_id;

        return User::select('id', 'name', 'company_id', 'email')
            ->with('company:id,code,name', 'roles')
            ->whereHas("roles", function ($q) {
                $q->whereIn("slug", ["coordinator", "coordinator-2"]);
            })
            ->whereHas('validatedExpenses', function($validatedQuery) use($coordinator_id, $start_date, $end_date) {
                $validatedQuery->when(isset($coordinator_id), function($coordinatorQuery) use($coordinator_id){
                    $coordinatorQuery->where('verified_by', $coordinator_id);
                })
                ->whereBetween('date_verified', [$start_date, $end_date]);
            })
            ->coordinatorValidatedExpense($start_date, $end_date, $company_id, $coordinator_id);
    }

    /**
     * Get all validate expenses per coordinator
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $expenses = ($this->expensePerUserCommonQuery($request))->orderBy('name', 'ASC')->paginate($request->limit);

        $expenses->getCollection()->transform(function ($item) {
            if (!$item) return;

            $verified_expense_count   = $item->verified_expenses_count;
            $rejected_expense_count   = $item->rejected_expenses_count;
            $validated_expense_count  = $item->validated_expenses_count;

            $verified = (new ExpenseController())->computeVerifiedAndRejected($item->validatedExpenses);
            $verified_amount = $verified['verified_amount'];
            $rejected_amount = $verified['rejected_amount'];
            $total_validated_amount = $verified['total_amount'];

            $data['id'] = $item->id;
            $data['name'] = $item->name;
            $data['company'] = isset($item->company) ? $item->company->name : '-';
            $data['validated_expense_count'] = $validated_expense_count;
            $data['verified_expense_count'] = $verified_expense_count;
            $data['rejected_expense_count'] = $rejected_expense_count;
            $data['total_validated_amount'] = $total_validated_amount;
            $data['verified_amount'] = $verified_amount;
            $data['rejected_amount'] = $rejected_amount;
            return $data;
        });

        return $expenses;
    }


    public function getValidatedExpenseStat(Request $request)
    {
        $expenses = ($this->expensePerUserCommonQuery($request))->has('expensesEntries')->get();

        $validated_expense_count   = 0;
        $verified_expense_count = 0;
        $rejected_expense_count = 0;

        $verified_amount = 0;
        $rejected_amount = 0;
        $total_validated_amount = 0;

        foreach ($expenses as $item) {
            if (count($item->validatedExpenses)) {
                // foreach ($item->validatedExpenses as $expenses) {
                    $validated_expense_count  = $validated_expense_count + $item->validated_expenses_count;
                    $verified_expense_count   = $verified_expense_count + $item->verified_expenses_count;
                    $rejected_expense_count   = $rejected_expense_count + $item->rejected_expenses_count;

                    $verified = (new ExpenseController())->computeVerifiedAndRejected($item->validatedExpenses);
                    $verified_amount = $verified_amount + $verified['verified_amount'];
                    $rejected_amount = $rejected_amount + $verified['rejected_amount'];
                    $total_validated_amount = $total_validated_amount + $verified['total_amount'];
                // }
            }
        }

        $verified_percentage = $validated_expense_count ? ($verified_expense_count / $validated_expense_count) * 100 : 0;
        $rejected_percentage = $validated_expense_count ? ($rejected_expense_count / $validated_expense_count) * 100 : 0;

        return [
            'validated_expense_count' => $validated_expense_count,
            'verified_expense_count' => $verified_expense_count,
            'rejected_expense_count' => $rejected_expense_count,
            'verified_amount' => $verified_amount,
            'total_validated_amount' => $total_validated_amount,
            'rejected_amount' => $rejected_amount,
            'verified_percentage' => round($verified_percentage),
            'rejected_percentage' => round($rejected_percentage),
        ];
    }
    
    public function show(Request $request, $user_id) {
        $start_date = "$request->start_date 00:00:01";
        $end_date = "$request->end_date 23:59:59";

        $expenses = Expense::with(
            'expensesType',
            'payments',
            'expenseVerificationStatus:id,name',
            'expenseRejectedRemarks:id,remark',
            'representaion:id,expense_id,attendees,purpose',
            'verifier:id,name',
            'routeTransportation:id,expense_id,from,to,transportation_id,remarks',
            'routeTransportation.transportation:id,mode',
            'grassroots:id,grassroots_expense_type_id,expense_id,remarks',
            'grassroots.grassrootExpenseType:id,name'
        )
        ->where('verified_by', $user_id)
            ->whereBetween('date_verified', [$start_date, $end_date])
            ->whereIn('verified_status_id', [1, 3])
            ->has('expensesEntry')
            ->withCount('history')
            ->get();

        return $expenses->transform(function ($item) {
            //Check if verification perion is expired
            $item['verification_perion_expired'] = (new ExpenseController())->isVerificationPeriodExpired($item->created_at);

            return $item;
        });

        return $expenses;
    }

    public function export(Request $request) {
        $today = date_format(now(), "M-d-Y");
        $month_year = strtoupper(date('F Y', strtotime($request->month_year)));
        $date_range = (new ExpenseController())->getWeekRangesOfMonthStartingMonday($request->month_year);
        return Excel::download(new CoordinatorValidatedReportExport($request, $date_range), "$month_year COORDINATOR VALIDATION REPORT - as of $today.xlsx");
    }
}
