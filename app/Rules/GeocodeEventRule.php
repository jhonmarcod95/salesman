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

    private $address;

    public function __construct()
    {

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
        $geoEventAddress = Geocoder::getCoordinatesForAddress($value);

        $result = true;

        // if not found above, customer name will not be included
        if($geoEventAddress['lat'] == 0 && $geoEventAddress['lng'] == 0){
            $this->address = $value;
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
        return 'Cannot locate ' . $this->address . ' coordinates, please update the address.';

    }
}
