<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PaymentsResource extends JsonResource
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
            'is_paid' => !empty($this->payments) ? 1 :0,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d')
        ];
    }
}
