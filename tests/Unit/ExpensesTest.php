<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Expense;

class ExpensesTest extends TestCase
{
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
}
