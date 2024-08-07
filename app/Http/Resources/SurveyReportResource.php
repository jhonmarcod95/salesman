<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Brand;

class SurveyReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $selected_brands = collect(json_decode(json_decode($this->brands, true)))->pluck('id');
        $brands  = collect(Brand::select('id','name','classification')->get())->whereIn('id',$selected_brands)->all();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'customer' => $this->customer,
            'classification' => $this->customer->classifications->description,
            'customer_photo' => $this->customer_photo,
            'brands' =>  $brands,
            'ranks' =>  json_decode(json_decode($this->ranks)),
            'remarks' => $this->remarks,
            'created_at' => (string) $this->created_at
        ];
    }
}
