<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseBypassResource extends JsonResource
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
            'created_by' => $this->created_by,
            'expenses_type_id' => $this->expenses_type_id,
            'status' => $this->status,
            'created_at' => (string)$this->created_at
        ];
    }
}
