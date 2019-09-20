<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CloseVisitResource extends JsonResource
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
            'user' => $this->user,
            'schedule' => $this->schedule,
            'confirmed_by' => $this->confirmedBy,
            'reason' => $this->reason,
            'isApproved' => $this->isApproved,
            'approved_date' => (string) $this->approved_date,
            'created_at' => (string) $this->created_at
        ];
    }
}
