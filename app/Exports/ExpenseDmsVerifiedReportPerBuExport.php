<?php

namespace App\Exports;

use App\ExpensesEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseDmsVerifiedReportPerBuExport implements FromCollection, WithHeadings {

    private $start_date, $end_date;
    public function __construct($request)
    {
        $this->start_date = date('Y-m-01 00:00:01', strtotime($request->month_year));
        $this->end_date = date('Y-m-t 23:59:59', strtotime($request->month_year));
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect($this->getData());
    }

    public function headings(): array {
        return [
            "Name",
            "Receipt Count",
            "Verified",
            "Rejected",
            "Unverified",
            "% Completion",
            "DMS Status",
            "Verify Status"
        ];
    }

    public function getData() {
        $expensesEntry = ExpensesEntry::with('user:id,name', 'user.companies', 'expensesModel:id,expenses_entry_id,dms_reference')
            ->expensePerMonth($this->start_date, $this->end_date)
            ->get();

        $expensesEntryData = [];
        foreach($expensesEntry as $item) {
            if($item->expenses_model_count) {
                $data['user'] = $item->user->name;
                $data['company'] = $item->user->companies[0]->name;
                $data['expenses_count'] = $item->expenses_model_count;
                $data['verified_count'] = $item->verified_expense_count;
                $data['unverified_count'] = $item->unverified_expense_count + $item->pending_expense_count;
                $data['rejected_count'] = $item->rejected_expense_count;
                $data['dms_status'] = !empty($item->expensesModel[0]) ? ($item->expensesModel[0]['dms_reference'] ? 'DMS Received' : 'Pending For Submission') : '';
                $expensesEntryData[] = $data;
            }
        }

        $expensesEntryData = collect($expensesEntryData)->groupBy('user');

        $expensesEntryPerUser = collect($expensesEntryData)->map(function($item) {
            $expenses_count = $item->sum('expenses_count');
            $verified_count = $item->sum('verified_count');
            $unverified_count = $item->sum('unverified_count');
            $rejected_count = $item->sum('rejected_count');
            $status = $item[0]['dms_status'];
            $completion_pecent = $expenses_count ? (($verified_count + $rejected_count) / $expenses_count) * 100 : '0';

            $verify_status = "";
            switch (true) {
                case $expenses_count == $verified_count:
                    $verify_status = "Completed";
                    break;
                case $verified_count > 0 && $rejected_count > 0 && $unverified_count == 0:
                    $verify_status = "Partially Completed";
                    break;
                case $unverified_count > 0:
                    $verify_status = "Pending";
                    break;
                case $expenses_count == $rejected_count:
                    $verify_status = "Rejected";
                    break;
            }

            return [
                "user" => $item[0]['user'], // Name
                "expenses_count" => $expenses_count ?: '0', // Receipt
                "verified_count" => $verified_count ?: '0', // Verified
                "rejected_count" => $rejected_count ?: '0', // Rejected
                "unverified_count" => $unverified_count ?: '0', // Unverified
                "completion_pecent" => $expenses_count ? (round($completion_pecent) ?: '0') : '0', //"% Completion
                "DMS Status" => $status,
                "Verify Status" => $verify_status
            ];
        });

        return collect($expensesEntryPerUser)->sortBy('user');
    }
}
