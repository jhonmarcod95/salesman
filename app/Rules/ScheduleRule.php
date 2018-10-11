<?php

namespace App\Rules;

use App\Schedule;
use Illuminate\Contracts\Validation\Rule;

class ScheduleRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $tsr_id;
    private $date;
    private $customer_code;

    public function __construct($date, $tsr_id, $customer_code)
    {
        $this->tsr_id = $tsr_id;
        $this->date = $date;
        $this->customer_code = $customer_code;
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
        $schedule = Schedule::where('tsr_id', $this->tsr_id)
            ->where('date', $this->date)
            ->where('customer_code', $this->customer_code)
            ->get();

        if(count($schedule)){
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
        return 'Customer is already added in the schedule.';
    }
}
