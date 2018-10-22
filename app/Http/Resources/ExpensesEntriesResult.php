<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ExpensesEntriesResult extends JsonResource
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
            'date_created' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'totalEntries' => count(json_decode($this->expenses)),
            'totalExpenses' => number_format($this->totalExpenses, 2),
        ];
    }
}
