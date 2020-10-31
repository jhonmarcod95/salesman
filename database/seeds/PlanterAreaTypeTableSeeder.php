<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PlanterAreaTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('planter_area_types')->insert([
            array(
                'name' => 'N/P',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'R1/R2/R3',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Empty',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
        ]);
    }
}
