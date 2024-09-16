<?php

namespace App\Exports;

use App\ExpensesEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseVerifiedReportPerBuExport implements FromCollection, WithHeadings {

    private $start_date, $end_date;
    public function __construct($request)
    {
        $this->start_date = $request->start_date." 00:00:01";
        $this->end_date = $request->end_date." 23:59:59";
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect($this->getData());
    }

    public function headings(): array {
        return [
            "BU",
            "No. Of Attachments",
            "Verified",
            "Rejected",
            "Open",
            "% Completion"
        ];
    }

    public function getData() {
        $expensesEntry = ExpensesEntry::whereBetween('created_at', [$this->start_date, $this->end_date])
            ->with('user:id,name', 'user.companies')
            ->withCount('expensesModel')
            ->withCount('verifiedExpense')
            ->withCount('unverifiedExpense')
            ->withCount('rejectedExpense')
            ->withCount('pendingExpense')
            ->get();

        $expensesEntryData = $expensesEntry->transform(function($item) {
            $data = [];
            $data['user'] = $item->user->name;
            $data['company'] = $item->user->companies[0]->name;
            $data['expenses_count'] = $item->expenses_model_count;
            $data['verified_count'] = $item->verified_expense_count;
            $data['unverified_count'] = $item->unverified_expense_count + $item->pending_expense_count;
            $data['rejected_count'] = $item->rejected_expense_count;
            return $data;
        })->groupBy('company');

        // $expensesEntryPerBU = collect($expensesEntryData)->groupBy('company');
        $expensesEntryPerBU = collect($expensesEntryData)->map(function($item) {

            $expenses_count = $item->sum('expenses_count');
            $verified_count = $item->sum('verified_count');
            $unverified_count = $item->sum('unverified_count');
            $rejected_count = $item->sum('rejected_count');
            $completion_pecent = (($verified_count + $rejected_count) / $expenses_count) * 100;

            return [
                "bu" => $item[0]['company'],
                "expenses_count" => $expenses_count,
                "verified_count" => $verified_count,
                "unverified_count" => $unverified_count,
                "rejected_count" => $rejected_count,
                "completion_pecent" => round($completion_pecent)
            ];
        });

        return $expensesEntryPerBU;
    }
}
