<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;     
use PhpOffice\PhpSpreadsheet\Shared\Date;  
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ExpenseDeductionSheet implements FromQuery, WithHeadings, ShouldAutoSize, WithStyles, WithMapping, WithChunkReading, WithTitle, WithColumnFormatting
{
    protected $filters;
    protected $sheetType;
    protected $targetDate;

    public function __construct($filters, $sheetType, $targetDate)
    {
        $this->filters = $filters;
        $this->sheetType = $sheetType; 
        $this->targetDate = $targetDate; 
    }

    public function title(): string { return $this->sheetType; }

    public function query()
    {
        $monthNum = $this->targetDate->month;
        $yearNum = $this->targetDate->year;
        $monthName = $this->targetDate->format('F');
        $selectFields = [
            'u.name', 'p.document_code', 'c.code as company_code', 'ph.ap_user', 
            'ph.document_type', 'ph.document_date', 'c.name as company_name', 
            'ph.vendor_code', 'ph.payment_terms', 'ex.created_at', 'ph.posting_date', 
            'ph.reference_number', 'ph.vendor_name', 'ph.header_text', 
            'ph.baseline_date', 'pd.item', 'pd.item_text', 'pd.gl_account', 
            'pd.description', 'ex.attachment', 'pd.assignment', 'pd.input_tax_code', 
            'pd.internal_order', 'ex.amount as amount', 'pd.charge_type', 'pd.business_area', 
            'pd.or_number', 'pd.supplier_name', 'pd.supplier_address', 
            'pd.supplier_tin_number', 'ex.id as exp_id',
            'exver.remark'
        ];
        if ($this->sheetType === 'REJECTED') {
            $selectFields[] = DB::raw('NULL as deduction_id');
            $selectFields[] = DB::raw('NULL as exp_amt');
            $selectFields[] = DB::raw('NULL as deduction_date');
        } else {
            $selectFields[] = 'ed.id as deduction_id';
            $selectFields[] = 'ed.balance_deducted_amount as exp_amt';
            $selectFields[] = 'ed.created_at as deduction_date';
        }

        $query = DB::table('expenses as ex')
            ->select($selectFields)
            ->leftJoin('users as u', 'ex.user_id', '=', 'u.id')
            ->leftJoin('company_user as cu', 'u.id', '=', 'cu.user_id')
            ->leftJoin('companies as c', 'cu.company_id', '=', 'c.id')
            ->leftJoin('expense_verification_rejected_remarks as exver', 'exver.id', '=', 'ex.expense_rejected_reason_id')
            ->leftJoin('payments as p', 'p.expense_id', '=', 'ex.id')
            ->leftJoin('payment_headers as ph', 'p.document_code', '=', 'ph.document_code')
            ->leftJoin('payment_details as pd', 'pd.payment_header_id', '=', 'ph.id')
            ->whereNull('ex.deleted_at')
            ->whereNull('p.deleted_at')
            ->whereNull('ph.deleted_at');

        if ($this->sheetType === 'REJECTED') {
            $query->whereIn('ex.verified_status_id',[2, 3])
                  ->whereMonth('ex.created_at', $monthNum)
                  ->whereYear('ex.created_at', $yearNum);
                  
            $query->whereNotExists(function ($subQuery) use ($monthName, $yearNum) {
                $subQuery->select(DB::raw(1))
                    ->from('employee_monthly_expenses as eme')
                    ->join('expense_deductions as ed', 'ed.employee_monthly_expense_id', '=', 'eme.id')
                    ->whereRaw('ed.expense_id = ex.id')
                    ->where('eme.month', $monthName)
                    ->where('eme.year', $yearNum)
                    ->whereNull('eme.deleted_at')
                    ->whereNull('ed.deleted_at');
            });

        } else {
            $query->join('expense_deductions as ed', 'ed.expense_id', '=', 'ex.id')
                  ->join('employee_monthly_expenses as eme', function ($join) use ($monthName, $yearNum) {
                      $join->on('ed.employee_monthly_expense_id', '=', 'eme.id')
                           ->where('eme.month', $monthName)
                           ->where('eme.year', $yearNum);
                  })
                  ->whereNull('ed.deleted_at')
                  ->whereNull('eme.deleted_at');
        }


        return $query->when($this->filters['company_id'] ?? null, function ($q, $id) {
                return $q->where('c.id', $id);
            })
            ->when($this->filters['user_id'] ?? null, function ($q, $id) {
                return $q->where('u.id', $id);
            })
            ->groupBy('ex.id')
            ->orderBy('u.name', 'asc');
    }

    public function map($row): array
    {
        $baseURL = "http://salesforce.lafilgroup.net:8666/storage/";
        $fullPath = $baseURL . $row->attachment;
        
        return [
            $row->name, 
            $row->document_code, 
            $row->company_code, 
            $row->ap_user, 
            $row->document_type,
            $row->document_date ? Date::dateTimeToExcel(Carbon::parse($row->document_date)) : '',
            $row->company_name, 
            $row->vendor_code, 
            $row->payment_terms,
            $row->created_at ? Date::dateTimeToExcel(Carbon::parse($row->created_at)) : '',
            $row->posting_date ? Date::dateTimeToExcel(Carbon::parse($row->posting_date)) : '',
            $row->reference_number, 
            $row->vendor_name, 
            $row->header_text, 
            $row->baseline_date ? Date::dateTimeToExcel(Carbon::parse($row->baseline_date)) : '',
            $row->item, 
            $row->item_text, 
            $row->gl_account, 
            $row->description, 
            $fullPath ? '=HYPERLINK("' . $fullPath . '", "View")' : '',
            $row->assignment, 
            $row->input_tax_code,
            $row->internal_order, 
            (float) $row->amount, 
            $row->charge_type, 
            $row->business_area,
            $row->or_number, 
            $row->supplier_name, 
            $row->supplier_address, 
            $row->supplier_tin_number,
            $row->exp_id, 
            $row->remark, 
            (float) ($row->exp_amt ?? $row->amount), 
            $row->deduction_date ? Date::dateTimeToExcel(Carbon::parse($row->deduction_date)) : ''
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'O' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'W' => '#,##0.00',
            'AF' => '#,##0.00',
            'AG' => 'yyyy-mm-dd hh:mm:ss',
        ];
    }

    public function styles(Worksheet $sheet) 
    { 
        $sheet->freezePane('B2');
        $sheet->getStyle('1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('T2:T' . $highestRow)->applyFromArray([
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'],
                    'bold' => true,
                    'underline' => Font::UNDERLINE_NONE,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
        }

        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension('A' . $col)->setAutoSize(true);
        }
    }


    public function headings(): array 
    { 
        return [
            'Name', 'Document Code', 'Company Code', 'AP User', 'Document Type', 'Document Date', 
            'Company Name', 'Vendor Code', 'Payment Terms', 'Transaction Date', 'Posting Date', 
            'Reference Number', 'Vendor Name', 'Header Text', 'Baseline Date', 'Item', 'Item Text', 
            'GL Account', 'Description', 'Attachment Link', 'Assignment', 'Input Tax Code', 'Internal Order', 'Amount', 
            'Charge Type', 'Business Area', 'OR Number', 'Supplier Name', 'Supplier Address', 
            'Supplier TIN Number','Expense ID', 'Rejected Reason', 'Rejected Deducted Amount', 'Deduction Date'
        ]; 
    }

    public function chunkSize(): int { return 1000; }
}