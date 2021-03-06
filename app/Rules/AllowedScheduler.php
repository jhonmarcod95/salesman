<?php

namespace App\Rules;

use App\ManageSchedule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AllowedScheduler implements Rule
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
        $scheduler_id = Auth::user()->id;
        $user_id = $value;

        if (ManageSchedule::where('scheduler_id', $scheduler_id)->exists()){
            if (ManageSchedule::where('scheduler_id', $scheduler_id)
                ->where('user_id', $user_id)
                ->exists()){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return true;
        }

        //
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are not allowed to assign or change schedule for this user.';
    }
}
