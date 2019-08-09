<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SchedulesResource extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address,
            'date' => Carbon::parse($this->date)->format('M d'),
            'raw_date' => $this->date,
            'start_time' => date('h:i A', strtotime($this->start_time)),
            'end_time' => date('h:i A', strtotime($this->end_time)),
            'isCurrent' => $this->isCurrent,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'km_distance' => $this->km_distance,
            'type' => $this->type
        ];
    }
}
