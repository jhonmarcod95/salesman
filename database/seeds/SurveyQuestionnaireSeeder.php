<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SurveyQuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('survey_questionnaires')->insert([
            array(
                'user_id' => 35,
                'survey_header_id' => 1,
                'question' => 'How satisfied are you with the performance of our customer service representative?',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            array(
                'user_id' => 35,
                'survey_header_id' => 1,
                'question' => 'How responsive have we been to your questions or concerns about our products?',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
        ]);
    }
}
