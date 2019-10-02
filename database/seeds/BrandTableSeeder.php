<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brands')->insert([
            array(
                'name' => 'Montana Spring Hard Wheat Flour',
                'classification' => 'Hard Wheat Flour',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Dakota Champion Hard Wheat Flour',
                'classification' => 'Hard Wheat Flour',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Washington Gold Hard Wheat Flour',
                'classification' => 'Hard Wheat Flour',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Gold Key Soft Wheat Flour',
                'classification' => 'Soft Wheat Flour',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Amigo Gold Soft Wheat Flour',
                'classification' => 'Soft Wheat Flour',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Snow Silk Cake Flour',
                'classification' => 'Cake Flour',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Family All-Purpose Flour',
                'classification' => 'All Purpose Flour',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
        ]);
    }
}
