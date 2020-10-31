<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
            'area' => number_format($this->area, 2),
            'date_planted' => Carbon::parse($this->date_planted)->format('m/d/Y'),
            'date_estimate_harvest' => Carbon::parse($this->date_estimate_harvest)->format('m/d/Y'),
            'crop_tech_remarks' => $this->crop_tech_remarks,
            'area_converted' => $this->area_converted,
            'variety' => $this->variety,
            'remarks' =>  $this->remarks,
            'assistance_needed' =>  json_decode($this->assistance_needed, true),
            'area_plant_type' =>  $this->planterAreaType,
            'soil_type' =>  $this->planterSoilType,
            'soil_condition' =>  $this->planterSoilConditionType,
            'planter_crop_type' =>  $this->planterCropType,
            'bir_id' =>  $this->bir_id,
            'planter_picture' =>  $this->planter_picture,
            'parcellary' =>  $this->parcellary,
            'created_at' => (string) $this->created_at
        ];
    }
}
