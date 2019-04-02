<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\SalesmanInternalOrder;
class ChargeTypeRule implements Rule
{
    protected $userId;
    protected $id;
    protected $action;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId, $id = null, $action = null)
    {
        $this->userId = $userId;
        $this->id = $id;
        $this->action = $action;
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
        $internal_order = $this->action == 'Edit' ? SalesmanInternalOrder::where('id', '!=', $this->id)->where('user_id',$this->userId)->where('charge_type', $value)->first() : SalesmanInternalOrder::where('user_id',$this->userId)->where('charge_type', $value)->first();

        if($internal_order){
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
        return 'Expense type already exist';
    }
}
