<?php

use Illuminate\Database\Seeder;

class AapcInsectTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aapc_insect_types')->insert([
            array('name' => 'Thrips'),
            array('name' => 'Aphids'),
            array('name' => 'Fruit borer'),
            array('name' => 'DBM'),
            array('name' => 'White Flies'),
            array('name' => 'FAW'),
        ]);
    }
}
