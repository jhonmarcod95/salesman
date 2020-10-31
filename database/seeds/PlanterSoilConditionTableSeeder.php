<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PlanterSoilConditionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('planter_soil_conditions')->insert([
            array(
                'name' => 'HILLY',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'FLAT',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
        ]);
    }
}
