<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Expense;
use App\ExpenseRate;
use App\ExpensesType;

class AmountLimit implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $expenses_type_id;
    private $expense_id;

    public function __construct($expenses_type_id, $expense_id)
    {
        $this->expenses_type_id = $expenses_type_id;
        $this->expense_id = $expense_id;
    }

    /**
     * Query Expense based from expenses_type or if has expense_id
     *
     * @param [type] $expense_id
     * @return void
     */
    public function queryExpense($expense_id) {

        $get_expenses = Expense::where('user_id', Auth::user()->id)
                            ->whereNotIn('id', [$expense_id])
                            ->where('expenses_type_id', $this->expenses_type_id)
                            ->where('created_at', '>=', Carbon::today())
                            ->get();

        return $get_expenses;

    }

    /**
     * Get the total food expenses in a day
     *
     * @param [type] $new_value
     * @return void
     */
    public function getTotalFoodExpenses($new_value) {

        $response = $this->queryExpense($this->expense_id)->reduce(function ($total, $item) {
            return $total + $item->amount;
        });

        $new_response = $response + $new_value;

        return $new_response;

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

        return $value <= ExpensesType::find($this->expenses_type_id)->amount_rate && $this->getTotalFoodExpenses($value)
                        <= ExpenseRate::rateAmount($this->expenses_type_id);

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Budget exceeded';
    }
}
