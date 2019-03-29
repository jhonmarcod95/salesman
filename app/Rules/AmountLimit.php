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

    private $expenses_type_id; // determine the expense type [store & update]
    private $expense_id; // determine the expense_id for [update] function only
    private $io_balance; // get the budget balance from SAP
    // private $remaining_balance; // pass the $value argument to new remaining balance

    public function __construct($expenses_type_id, $expense_id, $io_balance)
    {
        $this->expenses_type_id = $expenses_type_id;
        $this->expense_id = $expense_id;
        $this->io_balance = $io_balance;
    }

    /**
     * Query Expense based from expenses_type or if has expense_id within the day
     *
     * @param [type] $expense_id
     * @return void
     */
    public function queryExpense($expense_id) {

        $get_expenses = Expense::where('user_id', Auth::user()->id)
                            ->whereNotIn('id', [$expense_id]) // will take effect on update function
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
    public function getTodaysExpense($new_value) {

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

        // default or maximum amount per expense type
        // daily limit
        $defaultExpenseRate = ExpensesType::find($this->expenses_type_id)->amount_rate;

        // determine if has maintained custom expense rate
        // daily limit
        $maintainedExpenseRate = ExpenseRate::rateAmount($this->expenses_type_id);

        // set default budget total
        $budgetBalanceCurrent = 0;
        // if io_balance is found
        if($this->io_balance || $this->io_balance ==  'N/A') {
            $budgetBalanceCurrent = (double) $this->io_balance - $this->getTodaysExpense($value);

            // If user has SAP budget line assigned
            if($maintainedExpenseRate->exists()) {

                return $this->io_balance ? $budgetBalanceCurrent > 0 &&
                    $this->getTodaysExpense($value) <= $maintainedExpenseRate->pluck('amount')->first() :
                    $this->getTodaysExpense($value) <= $maintainedExpenseRate->pluck('amount')->first();

            } else {

                return $this->io_balance ? $budgetBalanceCurrent > 0 &&
                    $this->getTodaysExpense($value) <= $defaultExpenseRate :
                    $this->getTodaysExpense($value) <= $defaultExpenseRate;

            }
        } else {
            // User can't proceed / no budget line found
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
        if($this->io_balance || $this->io_balance ==  'N/A') {
            return 'Budget Exceeded';
        } else {
            return 'No Budget Line Found';
        }
    }
}
