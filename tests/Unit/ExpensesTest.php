<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Expense;
use App\ExpenseRate;
use App\ExpensesType;
use App\ExpenseChargeType;
use App\SalesmanInternalOrder;

class ExpensesTest extends TestCase
{

    // To run test
    // vendor\bin\phpunit --filter [methodName]

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testAmountLimit()
    {

        $expense_type_id = 1; // test value for expense_type_id

        $get_food_expenses = Expense::where('user_id', 35) // My User Id
                    ->where('expenses_type_id', $expense_type_id)
                    ->where('created_at', '>=', Carbon::today())
                    ->get();

        // get total daily expense
        $response = $get_food_expenses->reduce(function ($total, $item) {
            return $total + $item->amount;
        });

        $new_response = $response + $new_value;

        //  echo json_encode($response, JSON_PRETTY_PRINT);
        $this->assertNotNull($response);
        $this->assertLessThan(175, $response);

    }

    public function testQueryExpenses()
    {
        $user_id = 35; // the current user login in the app
        $expense_id = 1783; // expense id from data
        $expenses_type_id = 1; // expense type

        $get_expenses = Expense::where('user_id', $user_id)
                            ->whereNotIn('id', [$expense_id])
                            ->where('expenses_type_id', $expenses_type_id)
                            ->where('created_at', '>=', Carbon::today())
                            ->get();

        echo json_encode($get_expenses->count(), JSON_PRETTY_PRINT);
        $this->assertGreaterThan(0, $get_expenses->count());

    }

    public function testExpenseRate()
    {
        $expenses_type_id = 4; // lodging expense
        $user_id = 35; // current user id

        $query = ExpenseRate::where('user_id', $user_id)
                            ->where('expenses_type_id', $expenses_type_id);

        $default_rate = $query->exists() ?
                        $query->pluck('amount')->first() :
                        ExpensesType::find($expenses_type_id)->amount_rate;

        echo json_encode($default_rate, JSON_PRETTY_PRINT);

        $this->assertGreaterThan(5000, $default_rate);
    }

    public function testCheckUserBalance()
    {
        // given values
        $user = 35; // Auth::user()->id

        $internalOrders = SalesmanInternalOrder::where('user_id', $user);

        //assert if has value & count result
        $this->assertGreaterThan(0, $internalOrders->count());
    }

    public function testChargeTypes()
    {
        $expense_type = 1;
        $expenseChargeType = ExpenseChargeType::where('expense_type_id', $expense_type);

        $this->assertTrue($expenseChargeType->exists());
    }

    public function testCheckInternalOrder()
    {
        $chargeType = 'A1'; // given charge type
        $user_id = 35; // current authenticated user

        $io = SalesmanInternalOrder::
                where('user_id', $user_id)
                ->where('charge_type', $chargeType);

        $this->assertTrue($io->exists());

    }

    public function testFindExpense()
    {
        $expense_id = 100;
        $expense = Expense::whereId($expense_id)->whereNotIn('expenses_type_id',[1,3]); // Food, Lodging
        $this->assertFalse($expense->exists());
    }

}
