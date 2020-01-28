<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SurveyHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('survey_headers')->insert([
            array(
                'header' => 'Customer Service Representative',
                'company_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            )
        ]);
    }
}
