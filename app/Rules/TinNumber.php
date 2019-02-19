<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TinNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
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
        // removed underscore, total length is equal to 11 characters
        $formatted_tin = trim(str_replace('_','', $value));

        // check if the character is still 11 after trim function, then return true
        if(strlen($formatted_tin) == 11) {
            return true;
        } else {
            // return false by default when didn't pass the validation
            return false;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute format is incomplete';
    }
}
