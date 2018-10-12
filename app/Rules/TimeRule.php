<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class TimeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $start_time;
    private $end_time;


    public function __construct($start_time, $end_time)
    {
        $this->start_time = $start_time;
        $this->end_time = $end_time;
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
        if(Carbon::parse($this->start_time) > Carbon::parse($this->end_time)){
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The start time must no be greater than end time.';
    }
}
