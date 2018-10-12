<?php

use Illuminate\Database\Seeder;

class ExpensesTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('expenses_types')->insert([
            array('name' => 'Food'),
            array('name' => 'Freight & Handling'),
            array('name' => 'Lodging'),
            array('name' => 'Repairs & Maintenance'),
            array('name' => 'Entertainment Expenses'),
            array('name' => 'Seminar Expenses'),
        ]);
    }
}
