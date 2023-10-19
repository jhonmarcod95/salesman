<?php

use Illuminate\Database\Seeder;

class AapcCropsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aapc_crops')->insert([
            array('name' => 'Rice'),
            array('name' => 'Sugarcane'),
            array('name' => 'Corn'),
            array('name' => 'Lowland Vegetable'),
            array('name' => 'Highland Vegetable'),
        ]);
    }
}
