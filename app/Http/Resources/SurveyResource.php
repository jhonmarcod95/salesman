<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
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
            'customer' => $this->customer,
            'ranks' => $this->ranks,
            'brands' => $this->brands,
            'remarks' => $this->remarks,
            'customer_photo' => $this->customer_photo,
        ];
    }
}
