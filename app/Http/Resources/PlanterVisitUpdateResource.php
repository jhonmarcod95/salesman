<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PlanterVisitUpdateResource extends JsonResource
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
            'planter_name' => $this->planter_name,
            'contact_number' => $this->contact_number,
            'planter_address' => $this->planter_address,
            'hacienda_loc' => $this->hacienda_loc,
            'total_area' => (int) trim($this->total_area),
            'area' => $this->area,
            'date_planted' => Carbon::parse($this->date_planted)->format('Y-m-d H:i:s'),
            'date_estimate_harvest' => Carbon::parse($this->date_estimate_harvest)->format('Y-m-d H:i:s'),
            'crop_tech_remarks' => $this->crop_tech_remarks,
            'area_converted' => (int) trim($this->area_converted),
            'variety' => $this->variety,
            'remarks' =>  $this->remarks,
            'assistance_needed' =>  json_decode($this->assistance_needed, true),
            'planter_area_type_id' =>  $this->planterAreaType,
            'planter_soil_type_id' =>  $this->planterSoilType,
            'planter_soil_condition_id' =>  $this->planterSoilConditionType,
            'planter_crop_type_id' =>  $this->planterCropType,
            'bir_id' =>  $this->bir_id,
            'planter_photo' =>  $this->planter_picture,
            'parcellary_photo' =>  $this->parcellary,
            'created_at' => (string) $this->created_at
        ];
    }
}
