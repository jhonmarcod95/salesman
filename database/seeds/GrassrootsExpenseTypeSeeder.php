<?php

use Illuminate\Database\Seeder;

class GrassrootsExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grassroots_expense_types')->insert([
            array('name' => 'Food','amount_rate' => 80),
            array('name' => 'Lodging','amount_rate' => 300),
            array('name' => 'Sound System','amount_rate' => 1500),
            array('name' => 'Mascot','amount_rate' => 1000),
            array('name' => 'Candies','amount_rate'=> 300),
            array('name' => 'Balloons', 'amount_rate' => 300),
            array('name' => 'Venue','amount_rate' => 10000),
            array('name' => 'Permit', 'amount_rate' => 1500),
            array('name' => 'Rent', 'amount_rate' => 3000),
        ]);
    }
}
