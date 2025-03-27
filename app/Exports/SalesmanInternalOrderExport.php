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
            'id',
            'Internal Order',
            'Name',
            'Company',
            'Server',
            'Expense Type',
            'Charge Type',
            'Amount Rate',
            'UOM',
            'GL Account ID',
            'Created',
        ];
    }
    public function getData()
    {
        $selectedData = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'internal_order' => $item['internal_order'],
                'user' => $item['user']['name'],
                'company' => $item['user']['company']['name'] .' - '. $item['user']['company']['code'],
                'sap_server' => $item['sap_server'],
                'expense_type' => $item['charge_type']['expense_charge_type']['expense_type']['name'] ?? '-',
                'charge_type' => $item['charge_type']['name'] ?? '-',
                'amount_rate' => $item['charge_type']['expense_charge_type']['expense_type']['amount_rate'] ?? '-',
                'uom' => $item['uom'],
                'gl_account_id' => $item['gl_account_id'],
                'created_at' => $item['created_at'],
            ];
        }, $this->data);
        return $selectedData;
    }
}
