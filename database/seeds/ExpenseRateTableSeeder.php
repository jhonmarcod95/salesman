<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExpenseRateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('expense_rates')->insert([
            array(
                'created_by' => 35,
                'user_id' =>  19,
                'expenses_type_id' => 3,
                'amount' => 1500,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'created_by' => 35,
                'user_id' =>  11,
                'expenses_type_id' => 3,
                'amount' => 1500,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'created_by' => 35,
                'user_id' =>  34,
                'expenses_type_id' => 3,
                'amount' => 1500,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'created_by' => 35,
                'user_id' =>  20,
                'expenses_type_id' => 3,
                'amount' => 1500,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'created_by' => 35,
                'user_id' =>  5,
                'expenses_type_id' => 3,
                'amount' => 1500,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'created_by' => 35,
                'user_id' =>  40,
                'expenses_type_id' => 3,
                'amount' => 2500,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
        ]);
    }
}
