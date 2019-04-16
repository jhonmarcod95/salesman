<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Faker\Factory;
use App\ExpenseBypass;

class ExpenseBypassTest extends TestCase
{
    /**
     * @test
     */
    public function can_create_expense_bypass()
    {
        $faker = Factory::create();

        $created_by = 35; // Admin
        $expenses_type_id = 1; // Food
        $user = 35; // Admin
        $status = 1; // default status

            $response = $this->actingAs($this->defaultUser(), 'api')->json('POST', 'api/expense-bypass', [
                'user_id' => $user,
                'created_by' => $created_by,
                'expenses_type_id' => $expenses_type_id,
                'remarks' => $remarks = "Test Entries Only",
                'status' => $status
            ]);

            $response
            ->assertJsonStructure([
                'user_id','created_by','expenses_type_id','status','created_at'
            ])
            ->assertJson([
                'user_id' => $user,
                'created_by' => $created_by,
                'expenses_type_id' => $expenses_type_id,
                'status' => $status
            ])
            ->assertStatus(201);

            $this->assertDatabaseHas('expense_bypasses',[
                'user_id' => $user,
                'created_by' => $created_by,
                'expenses_type_id' => $expenses_type_id,
                'status' => $status
            ]);


    }

    /**
     * @test
     */
    public function can_bypass_a_food_expense()
    {
        $user = 35; // test user
        $expenses_type_id = 1; // Food

        if (Auth::attempt(['email' => 'terrence.tejada@lafilgroup.com', 'password' => 'password'])) {

            $response = ExpenseBypass::bypass($expenses_type_id)->exists();

            // echo json_encode($response, JSON_PRETTY_PRINT);

            $this->assertTrue($response);
        }
    }

    /**
     * @test
     */
    public function cannot_bypass_a_non_food_expense()
    {
        $user = 35; // test user
        $expenses_type_id = 69; // Food

        if (Auth::attempt(['email' => 'terrence.tejada@lafilgroup.com', 'password' => 'password'])) {

            $response = ExpenseBypass::bypass($expenses_type_id)->exists();

            // echo json_encode($response, JSON_PRETTY_PRINT);

            $this->assertFalse($response);
        }
    }
}

