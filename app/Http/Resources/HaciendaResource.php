<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HaciendaResource extends JsonResource
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
            'planter_code' => $this->planter_code,
            'name' => $this->name,
            'mobile_number' => strlen($this->mobile_number) == 11 ?  substr($this->mobile_number,1) : $this->mobile_number, 1,
            'planter_audit_no' => $this->planter_audit_no,
            'address' => $this->address,
            'area' => $this->area,
            'planter' => new PlanterVisitUpdateResource($this->planter) 
        ];
    }
}
