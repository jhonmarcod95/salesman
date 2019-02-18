<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TinNumbersResource extends JsonResource
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
            'tin_number' => $this->tin_number,
            'vendor_name' => $this->vendor_name,
            'vendor_address' => $this->vendor_address,
        ];
    }
}
