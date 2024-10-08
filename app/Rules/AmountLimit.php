<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Expense;
use App\ExpenseRate;
use App\ExpensesType;
use App\ExpenseBypass;
use App\User;

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
        $this->returnMessage = 'Budget Exceeded';
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
     * Get all unprocess expenses within the month
     *
     * @param [integer] $expenses_type_id
     *
     * @return json
     *
     */
    public function getUnprocessSubmittedExpense()
    {
        $expense = Expense::whereUserId(Auth::user()->id)
                        ->where('expenses_type_id',$this->expenses_type_id)
                        ->whereBetween('created_at', [Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])
                        ->doesntHave('postedPayments')
                        // ->has('expensesEntry')
                        ->get();

        return $expense->sum('amount');
    }

    /**
     * Get the user who created the expense entry
     */
    public function checkIfPliliAgent()
    {
        return User::where('id',Auth::user()->id)
                          ->whereHas('companies', function ($x) {
                                $x->whereIn('companies.id',[5]); // if has FMCG-PL
                            })->exists();
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
        // if user is FMCG-PL
        // if expense type is equal = 1 or food and the user is plili
        $plFoodBudget = 0;
        if($this->expenses_type_id == 1 && $this->checkIfPliliAgent()) {
            $plFoodBudget = ExpensesType::find(25)->exists() ? ExpensesType::find(25)->amount_rate : 150; // 150; // Food-PL amount
        }
        // default or maximum amount per expense type
        // daily limit
        $defaultExpenseRate = $plFoodBudget > 0 ? $plFoodBudget : ExpensesType::find($this->expenses_type_id)->amount_rate;

        // determine if has maintained custom expense rate
        // daily limit
        $maintainedExpenseRate = ExpenseRate::rateAmount($this->expenses_type_id);

        //Check if user has a expense exception to bypass budget exceeded
        $expenseBypass = ExpenseBypass::bypass($this->expenses_type_id);

        // set default budget total
        $budgetBalanceCurrent = 0;

        // Check if user has expense bypass
        if($expenseBypass->exists()) {
            return true;
        }

        if($this->io_balance == "QTYLIMIT") {
            $this->returnMessage = 'Quantity budget exceeded. Contact Sales Coordinator.';
            return false;
        }

        // if budget is low vs amount but has a qty checking value yet. proceed to save.
        if($this->io_balance == "QTYPASSED") {

            // If user has SAP budget line assigned
            $isMaintainedExpenseRate = $maintainedExpenseRate->exists() ?
                                        $maintainedExpenseRate->pluck('amount')->first() :
                                        $defaultExpenseRate;
            
            if(($value <= $isMaintainedExpenseRate) == false) {
                $this->returnMessage = "The daily allocated budget has been exceeded!";
                return false;
            }

            return true;
        }

        // do not proceed if io_balance is empty
        // if io_balance is found
        if($this->io_balance || $this->io_balance === 'N/A') {

            // $simulatedBalance =  max((double) $this->io_balance - $this->getTodaysExpense($value), 0);

            // Get simulated balance SAP Balance - Unposted Expense = Simulated balance
            $simulatedBalance = (float) $this->io_balance;

            // If user has SAP budget line assigned
            $isMaintainedExpenseRate = $maintainedExpenseRate->exists() ? $maintainedExpenseRate->pluck('amount')->first() : $defaultExpenseRate;

            // default condition if no budget line found from the user
            if($this->io_balance == 'N/A') {
                return $this->getTodaysExpense($value) <= $defaultExpenseRate;
            }

            switch(false) {
                case (1 <= $value): // minimum input amount is 1
                    $this->returnMessage = 'Invalid amount';
                    return false;
                    break;
                case ($simulatedBalance >= $value): // simulated budget ( SAP - unposted = simulated) should > value
                    $this->returnMessage = 'Budget exceeded, please check your remaining balance';
                    return false;
                    break;
                case ($this->getTodaysExpense($value) <= $isMaintainedExpenseRate): // total expense for given expense type should be <= max set for the given expense type
                    $this->returnMessage = "Allocated budget today for this expense type is exceeded";
                    return false;
                    break;
                default:
                    return true;
            }


        } else {
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
        if($this->io_balance || $this->io_balance == 'N/A') {
            return $this->returnMessage;
        } else {
            return 'No Budget Line Found';
        }
    }
}
