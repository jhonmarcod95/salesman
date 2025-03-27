<?php

namespace App\Exports;

use App\SalesmanInternalOrder;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesmanInternalOrderExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->getData());
    }
    public function headings() : array
    {
        return [
            'Internal Order',
            'Name',
            'Email',
            'Company',
            'Server',
            'Expense Type',
            'Charge Type',
            'Amount Limit Per Day',
            'UOM',
            'GL Account',
            'Created',
        ];
    }
    public function getData()
    {
        $selectedData = array_map(function ($item) {
            return [
                'internal_order' => $item['internal_order'],
                'user' => $item['user']['name'],
                'email' => $item['user']['email'],
                'company' => $item['user']['company']['name'],
                'sap_server' => $item['sap_server'],
                'expense_type' => $item['charge_type']['expense_charge_type']['expense_type']['name'] ?? '-',
                'charge_type' => $item['charge_type']['name'] ?? '-',
                'amount_rate' => $item['user']['expense_rate'][0]['amount'] ?? '-',
                'uom' => $item['uom'],
                'gl_account' => $item['gl_account'] ? $item['gl_account']['code'] .' - '. $item['gl_account']['name'] : '-',
                'created_at' => $item['created_at'],
            ];
        }, $this->data);
        return $selectedData;
    }
}
