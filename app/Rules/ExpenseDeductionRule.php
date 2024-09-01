<?php

namespace App\Rules;

use App\Expense;
use Illuminate\Contracts\Validation\Rule;

class ExpenseDeductionRule implements Rule
{
    private $expense_id, $expense_amount;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($expense_id)
    {
        $this->expense_id = $expense_id;
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
        $deducted_amount = $value;
        $expense_amount = (Expense::find($this->expense_id))->amount;

        if($expense_amount < $deducted_amount) {
            $this->expense_amount = $expense_amount;
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
        return "The deduction amount exceeds the declared amount (PHP $this->expense_amount).";
    }
}
