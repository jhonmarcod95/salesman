<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Expense;
use App\ExpenseExclusive;
use App\ExpenseRate;
use App\ExpensesType;
use App\ExpenseChargeType;
use App\SalesmanInternalOrder;
use Ixudra\Curl\Facades\Curl;

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
        $user = 40; // Auth::user()->id // ale paez

        $internalOrders = SalesmanInternalOrder::orderBy('id', 'DESC')->where('user_id', $user);

        $expenseBalances = array();

         foreach($internalOrders->get() as $internalOrder) {

            // SAP API
            $response = Curl::to('http://10.96.4.39/salesforcepaymentservice/api/sap_budget_checking')
            ->withContentType('application/x-www-form-urlencoded')
            ->withData(array( 'budget_line' => $internalOrder->internal_order, 'posting_date' => Carbon::today()->format('m/d/Y'), 'company_server'=> $internalOrder->sap_server ))
            ->post();

            $toJson = json_decode($response, true);

            // Expense Charge Type
            $expenseChargeType = ExpenseChargeType::where('charge_type_id', $internalOrder->chargeType->id);

            $data = array(
                'id' => $internalOrder->id,
                'charge_type' => $internalOrder->charge_type,
                'internal_order' => $internalOrder->internal_order,
                'expense_type' => $expenseChargeType->exists() ? $expenseChargeType->first()->expenseType->name : null,
                'sap_server' => $internalOrder->sap_server,
                'balance' => (double) $toJson[0]['balance_amount']
            );
            array_push($expenseBalances,$data);

        }

        //  echo json_encode($expenseBalances, JSON_PRETTY_PRINT);

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

    /**
     * @test
     */
    public function check_internal_orders()
    {

        $response = $this->actingAs($this->defaultUser(), 'api')->json('GET', 'api/internal_orders');

        $response
        ->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);

    }

    /**
     * @test
     */
    public function check_current_expenses()
    {
        $response = $this->actingAs($this->defaultUser(), 'api')->json('GET', 'api/expenses');

        $response->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @test
     */
    public function check_unprocess_submitted_expenses()
    {
        $expenses_type_id = 3;  // lodging

        $response = $this->actingAs($this->defaultUser(), 'api')->json('GET', 'api/unprocess-expenses/'. $expenses_type_id);

        $response->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @test
     */
    public function can_store_expense()
    {

        $types = 19; // toll
        $amount = 800;

        $response = $this->actingAs($this->defaultUser(), 'api')
            ->json('POST', 'api/expenses', [
                'types' => $types,
                'amount' => $amount
            ]);

        \Log::info($response->getContent());

        $response
        ->assertJsonStructure([
            'id','currencyAmount','amount','types'
        ])
        ->assertJson([
            'amount' => $amount,
        ])
        ->assertStatus(200);

        $this->assertDatabaseHas('expenses',[
            'amount' => $amount,
        ]);


    }

    /**
     * @test
     */
    public function submitted_expenses()
    {

        $expenses_type_id = 1;

        $expense = Expense::whereUserId(160)
        ->where('expenses_type_id',$expenses_type_id)
        ->whereBetween('created_at', [Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])
        ->doesntHave('postedPayments')
        ->has('expensesEntry')
        ->get();

        $this->assertTrue(true);

        echo json_encode($expense, JSON_PRETTY_PRINT);
        echo "\n\n total Amount: ".json_encode($expense->sum('amount'), JSON_PRETTY_PRINT);
    }

    /**
     * @test
     */
    public function check_real_internal_orders()
    {

        $user = 160; //

        $response = $this->actingAs($this->defaultUser(), 'api')
            ->json('GET', 'api/real_internal_orders', [
                'user_id' => $user
            ]);

        $response
        ->assertStatus(200);


        echo json_encode($response, JSON_PRETTY_PRINT);

    }

    /**
     * @test
     */
    public function check_if_has_schedule()
    {
        $response = $this->actingAs($this->defaultUser(),'api')
            ->json('GET','api/schedules/exists');

        $response->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @test
     */
    public function check_if_has_visited_schedule()
    {
        $response = $this->actingAs($this->defaultUser(),'api')
            ->json('GET','api/schedules/has-visited');

        $response->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);
    }


    /**
     * @test
     */
    public function check_expense_restriction()
    {
        $expense_type = 20;

        $check_expense_type = ExpenseExclusive::where('expense_exlusibable_type', 'App\ExpensesTypes')->where('expense_exlusivable_id', $expense_type);

        $this->assertTrue(true);

        if ($check_expense_type->count() > 0) {
            $expnese_exclusive = collect(json_decode($check_expense_type->first()->users_array_id, true));
            $result =  in_array($this->defaultUser()->id, $expnese_exclusive->toArray()) ? 'true' : null;
            echo json_encode($result, JSON_PRETTY_PRINT);
        } else {
            echo json_encode('Expense type is not restricted', JSON_PRETTY_PRINT);
        }
    }

    /**
     * @test
     */
    public function check_expense_types()
    {
        $response = $this->actingAs($this->defaultUser(), 'api')
                    ->json('GET', '/api/expenses/types');

        $response->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}
