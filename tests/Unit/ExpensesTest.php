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
        $get_food_expenses = Expense::where('user_id', 35) // My User Id
                    ->where('expenses_type_id', 1)
                    ->where('created_at', '>=', Carbon::today())
                    ->get();

        $response = $get_food_expenses->reduce(function ($total, $item) {
            return $total + $item->amount;
        });

         echo json_encode($response, JSON_PRETTY_PRINT);
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

        $this->assertGreaterThan(8000, $default_rate);
    }
}
