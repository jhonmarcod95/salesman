<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        ];
    }
}
