<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\Carbon;

class ExpenseDeductionExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        $selectedDate = Carbon::parse($this->filters['month_year']);
        $currentMonthDate = Carbon::now(); 

        return [
            new ExpenseDeductionSheet($this->filters, 'RECOVERED', $selectedDate),
            new ExpenseDeductionSheet($this->filters, 'REJECTED', $selectedDate),
        ];
    }

}
