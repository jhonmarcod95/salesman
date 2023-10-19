<?php

use Illuminate\Database\Seeder;

class AapcVegetableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aapc_vegetables')->insert([
            array('name' => 'Eggplant (Talong)', 'vegetable_type' => 1),
            array('name' => 'Bittergourd (Ampalaya)', 'vegetable_type' => 1),
            array('name' => 'Chili labuyo', 'vegetable_type' => 1),
            array('name' => 'Chili sinigang', 'vegetable_type' => 1),
            array('name' => 'Tomato (Kamatis)', 'vegetable_type' => 1),
            array('name' => 'String beans (Sitaw)', 'vegetable_type' => 1),
            array('name' => 'Squash (Kalabasa)', 'vegetable_type' => 1),
            array('name' => 'Others', 'vegetable_type' => 1),
            array('name' => 'Potato (Patatas)', 'vegetable_type' => 2),
            array('name' => 'Cabbage (Repolyo)', 'vegetable_type' => 2),
            array('name' => 'Carrots (Karot)', 'vegetable_type' => 2),
            array('name' => 'Others', 'vegetable_type' => 2),
        ]);
    }
}
