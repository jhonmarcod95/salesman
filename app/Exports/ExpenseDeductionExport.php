<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery; // Changed to FromQuery for performance/structure
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping; // Added WithMapping for clarity
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class ExpenseDeductionExport implements FromQuery, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function columnFormats(): array
    {
        return [
            'V' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // PD Amount
            'AF' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Rejected Deducted Amount
            'E' => NumberFormat::FORMAT_DATE_YYYYMMDD,           // Document Date
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD,           // Posting Date
            'N' => NumberFormat::FORMAT_DATE_YYYYMMDD,           // Baseline Date
            'AG' => NumberFormat::FORMAT_DATE_YYYYMMDD,          // Should Be Posting Date
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD,           // Transaction Date
        ];
    }

    /**
     * Map the database result row to an array for the Excel row
     */
    public function map($row): array
    {
        // This ensures the column order is perfect for the headings
        return [
            $row->document_code, $row->company_code, $row->ap_user, $row->document_type, 
            $row->document_date, $row->company_name, $row->vendor_code, $row->payment_terms,
            $row->created_at, $row->posting_date, $row->reference_number, $row->vendor_name,
            $row->header_text, $row->baseline_date, $row->item, $row->item_text, 
            $row->gl_account, $row->description, $row->assignment, $row->input_tax_code,
            $row->internal_order, $row->amount, $row->charge_type, $row->business_area,
            $row->or_number, $row->supplier_name, $row->supplier_address, $row->supplier_tin_number,
            $row->dup, $row->exp_id, $row->attachment, $row->expense_rejected_reason_id,
            $row->rejected_deducted_amount, $row->should_be_posting_date
        ];
    }


    /**
     * Define the database query (returning the builder instance)
     */
    public function query()
    {
        $date = Carbon::parse($this->filters['month_year']);
        $monthName = $date->format('F');
        $year = $date->format('Y');

        return DB::table('employee_monthly_expenses as eme')
            ->join('users as u', 'eme.user_id', '=', 'u.id')
            ->join('company_user as cu', 'u.id', '=', 'cu.user_id')
            ->join('companies as c', 'cu.company_id', '=', 'c.id')
            ->join('expense_deductions as ed', 'eme.id', '=', 'ed.employee_monthly_expense_id')
            ->join('expenses as e', 'ed.expense_id', '=', 'e.id')
            ->join('payments as p', 'e.id', '=', 'p.expense_id')
            ->join('payment_headers as ph', function($join) {
                $join->on('ph.document_code', '=', 'p.document_code')
                     ->on('ph.company_code', '=', 'c.code');
            })
            ->join('payment_details as pd', 'pd.payment_header_id', '=', 'ph.id')
            ->select([
                'ph.document_code', 'c.code as company_code', 'ph.ap_user', 'ph.document_type', 
                'ph.document_date', 'c.name as company_name', 'ph.vendor_code', 'ph.payment_terms',
                'ph.created_at', 'ph.posting_date', 'ph.reference_number', 'ph.vendor_name',
                'ph.header_text', 'ph.baseline_date', 'pd.item', 'pd.item_text', 
                'pd.gl_account', 'pd.description', 'pd.assignment', 'pd.input_tax_code',
                'pd.internal_order', 'pd.amount', 'pd.charge_type', 'pd.business_area',
                'pd.or_number', 'pd.supplier_name', 'pd.supplier_address', 'pd.supplier_tin_number',
                'pd.supplier_name as dup', 'e.id as exp_id', 'e.attachment', 'e.expense_rejected_reason_id',
                'e.rejected_deducted_amount', 'e.should_be_posting_date'
            ])
            ->where('eme.month', $monthName)
            ->where('eme.year', $year)
            ->orderBy('ph.document_code', 'e.id', 'desc');
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);

        $highestRow = $sheet->getHighestRow();
        $mergeStart = 2;

        for ($i = 2; $i <= $highestRow; $i++) {
            $currentCode = $sheet->getCell("A{$i}")->getValue();
            $nextCode = $sheet->getCell("A" . ($i + 1))->getValue();

            if ($currentCode !== $nextCode || $i == $highestRow) {
                if ($i > $mergeStart) {
                    // Merge headers (Columns A to N) that stay constant for a document
                    foreach (range('A', 'N') as $col) {
                        $sheet->mergeCells("{$col}{$mergeStart}:{$col}{$i}");
                    }
                    // Merge expense/footer info (Columns AD to AH)
                    foreach (range('AD', 'AH') as $col) {
                        $sheet->mergeCells("{$col}{$mergeStart}:{$col}{$i}");
                    }
                    
                    $sheet->getStyle("A{$mergeStart}:AH{$i}")
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
                $mergeStart = $i + 1;
            }
        }
    }

    public function headings(): array
    {
        return [
            'PH Document Code', 'Company Code', 'AP User', 'Document Type', 'Document Date',
            'Company Name', 'Vendor Code', 'Payment Terms', 'Transaction Date', 'Posting Date',
            'Reference Number', 'Vendor Name', 'Header Text', 'Baseline Date', 'PD Item',
            'PD Item Text', 'GL Account', 'PD Description', 'PD Assignment', 'Input Tax Code',
            'Internal Order', 'PD Amount', 'Charge Type', 'Business Area', 'OR Number',
            'Supplier Name', 'Supplier Address', 'Supplier TIN Number', 'Supplier Name (Duplicate)',
            'Expense ID', 'Attachment', 'Rejected Reason ID', 'Rejected Deducted Amount', 'Deduction Date'
        ];
    }
}
