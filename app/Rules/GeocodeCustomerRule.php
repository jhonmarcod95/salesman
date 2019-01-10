<?php

namespace App\Rules;

use App\Customer;
use Illuminate\Contracts\Validation\Rule;
use Spatie\Geocoder\Facades\Geocoder;

class GeocodeCustomerRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $customer_name;

    public function __construct()
    {
        //
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
        $customer_codes = $value;

        $result = true;
        foreach ($customer_codes as $customer_code) {
            $customer = Customer::where('customer_code', $customer_code)->first();

            $geoCustomerNameAddress = Geocoder::getCoordinatesForAddress($customer->name.' - '.$customer->street.' - '.$customer->town_city);
            $geoCustomerAddress = Geocoder::getCoordinatesForAddress($customer->street.' - '.$customer->town_city);

            // combination of name + address
            if ($geoCustomerNameAddress['lat'] == 0 && $geoCustomerNameAddress['lng'] == 0){
                $this->customer_name = $customer->name;
                $result = false;
            }

            // if not found above, customer name will not be included
            else if($geoCustomerAddress['lat'] == 0 && $geoCustomerAddress['lng'] == 0){
                $this->customer_name = $customer->name;
                $result = false;
            }
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
        return 'Cannot locate customer ' . $this->customer_name . ' coordinates, please update the address.';
    }
}
