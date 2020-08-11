<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanterVisitResource extends JsonResource
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
            'planter_name' => $this->planter_name,
            'contact_number' => $this->contact_number,
            'planter_address' => $this->planter_address,
            'hacienda_loc' => $this->hacienda_loc,
            'total_area' => $this->total_area,
            'n_p' =>  json_decode($this->n_p, true),
            'r1_r2_r3' =>  json_decode($this->r1_r2_r3, true),
            'empty' =>  json_decode($this->empty, true),
            'assistance_needed' =>  json_decode($this->assistance_needed, true),
            'soil_type' =>  $this->planterSoilType,
            'soil_condition' =>  $this->planterSoilConditionType,
            'tons_yields' =>  $this->tons_yields,
            'tons_cane' =>  $this->tons_cane,
            'bir_id' =>  $this->bir_id,
            'planter_picture' =>  $this->planter_picture,
            'parcellary' =>  $this->parcellary,
        ];
    }
}
