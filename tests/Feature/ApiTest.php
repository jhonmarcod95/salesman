<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory;

class ApiTest extends TestCase
{
     /**
     * @test
     */
    public function can_request_to_close_visit()
    {

        $schedule_id = 1;

        $response = $this->actingAs($this->defaultUser(),'api')->json('POST','api/schedules/request-close', [
            'schedule_id' => $schedule_id
        ]);

        // \Log::info($response->getContent());

        $response
        ->assertJsonStructure([
            'id','schedule_id'
        ])
        ->assertJson([
            'schedule_id' => $schedule_id,
        ])
        ->assertStatus(201);

    }
}
