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
        $expensesEntry = ExpensesEntry::with('user:id,name', 'user.companies')
            ->expensePerMonth($this->start_date, $this->end_date)
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
            $completion_pecent = $expenses_count ? (round((($verified_count + $rejected_count) / $expenses_count) * 100)) : '0';

            return [
                "bu" => $item[0]['company'],
                "expenses_count" => $expenses_count ?: '0',
                "verified_count" => $verified_count ?: '0',
                "rejected_count" => $rejected_count ?: '0',
                "unverified_count" => $unverified_count ?: '0',
                "completion_pecent" => $completion_pecent ?: '0'
            ];
        });

        return $expensesEntryPerBU;
    }
}
