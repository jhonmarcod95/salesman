<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SurveyTest extends TestCase
{
    
     /**
     * @test
     */
    public function view_survey_detail()
    {
        $survey_id = 1;

        $response = $this->actingAs($this->defaultUser(), 'api')->json('GET',"api/surveys/1");

        \Log::info($response->getContent());

        //then
        $response->assertStatus(200);
        
        echo "\n\n".json_encode($response, JSON_PRETTY_PRINT);

    }

    /**
     * @test
     */
    public function view_survey_questionnaires()
    {
        $response = $this->actingAs($this->defaultUser(), 'api')
                        ->json('GET',"api/surveys/questionnaires");

        $response->assertStatus(200);

        echo "\n\n".json_encode($response, JSON_PRETTY_PRINT);
    }

}
