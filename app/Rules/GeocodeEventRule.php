<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\Geocoder\Facades\Geocoder;

class GeocodeEventRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $event_name;
    private $address;

    public function __construct($address)
    {
        $this->address = $address;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */

    public function passes($attribute, $value)
    {
        $event_name = $value;

        $geoEventNameAddress = Geocoder::getCoordinatesForAddress($event_name . ' ' . $this->address);
        $geoEventAddress = Geocoder::getCoordinatesForAddress($this->address);

        $result = true;

        // combination of name + address
        if ($geoEventNameAddress['lat'] == 0 && $geoEventNameAddress['lng'] == 0){
            $this->event_name = $event_name;
            $result = false;
        }

        // if not found above, customer name will not be included
        else if($geoEventAddress['lat'] == 0 && $geoEventAddress['lng'] == 0){
            $this->event_name = $event_name;
            $result = false;
        }
        return $result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Cannot locate ' . $this->event_name . ' coordinates, please update the address.';

    }
}
