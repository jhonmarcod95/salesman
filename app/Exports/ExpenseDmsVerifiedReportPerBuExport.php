<?php

namespace App\Exports;

use App\ExpensesEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseDmsVerifiedReportPerBuExport implements FromCollection, WithHeadings {

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
            "Name",
            "Receipt Count",
            "Verified",
            "Rejected",
            "Unverified",
            "% Completion",
            // "DMS Status"
        ];
    }

    public function getData() {
        $expensesEntry = ExpensesEntry::whereBetween('created_at', [$this->start_date, $this->end_date])
            ->with('user:id,name', 'user.companies', 'expensesModel:id,expenses_entry_id,dms_reference')
            ->withCount('expensesModel')
            ->withCount('verifiedExpense')
            ->withCount('unverifiedExpense')
            ->withCount('rejectedExpense')
            ->withCount('pendingExpense')
            ->get();

        $expensesEntryData = $expensesEntry->transform(function($item) {
            // dd($item->expensesModel[0]['dms_reference']);
            $data = [];
            $data['user'] = $item->user->name;
            $data['company'] = $item->user->companies[0]->name;
            $data['expenses_count'] = $item->expenses_model_count;
            $data['verified_count'] = $item->verified_expense_count;
            $data['unverified_count'] = $item->unverified_expense_count + $item->pending_expense_count;
            $data['rejected_count'] = $item->rejected_expense_count;
            // $data['dms_status'] = $item->expensesModel[0]['dms_reference'] ? 'DMS Received' : 'Pending';
            return $data;
        })->groupBy('user');

        $expensesEntryPerUser = collect($expensesEntryData)->map(function($item) {
            $expenses_count = $item->sum('expenses_count');
            $verified_count = $item->sum('verified_count');
            $unverified_count = $item->sum('unverified_count');
            $rejected_count = $item->sum('rejected_count');
            $completion_pecent = $expenses_count ? (($verified_count + $rejected_count) / $expenses_count) * 100 : '0';

            return [
                "user" => $item[0]['user'], // Name
                "expenses_count" => $expenses_count ?: '0', // Receipt
                "verified_count" => $verified_count ?: '0', // Verified
                "rejected_count" => $rejected_count ?: '0', // Rejected
                "unverified_count" => $unverified_count ?: '0', // Unverified
                "completion_pecent" => $expenses_count ? (round($completion_pecent) ?: '0') : '0' //"% Completion
                // "Status" => 
            ];
        });

        return collect($expensesEntryPerUser)->sortBy('user');
    }
}