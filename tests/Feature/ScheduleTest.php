<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory;

class ScheduleTest extends TestCase
{
    
    /**
     * @test
     */
    public function check_if_has_activity()
    {
        $response = $this->actingAs($this->defaultUser(), 'api')->json('GET',"api/schedules/has-activity");

        \Log::info($response->getContent());

        //then
        $response->assertStatus(200);
        
        echo "\n\n".json_encode($response, JSON_PRETTY_PRINT);

    }

}
