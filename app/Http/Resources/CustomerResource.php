<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->name . '-' . $this->street . '-' . $this->town_city,
            'customer_code' => $this->customer_code,
            'address' => $this->street . '-' . $this->town_city,
            'customer_name' => $this->name
        ];
    }
}
