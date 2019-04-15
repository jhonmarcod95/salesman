<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class APIExpenseResult extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'currencyAmount' => number_format($this->amount, 2),
            'amount' => $this->amount,
            'types' => $this->expensesType->name,
            'attachment' => $this->attachment,
            'expenses_type_id' => $this->expenses_type_id,
            'date_created' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'is_receipt_identified' => $this->receiptExpenses()->exists(),
            'receipt_expense_id' => $this->receiptExpenses()->exists() ? $this->receiptExpenses->id : 0,
            'is_route' => $this->routeTransportation()->exists(),
            'route_transportation_id' => $this->routeTransportation()->exists() ? $this->routeTransportation->id : 0,
        ];
    }
}
