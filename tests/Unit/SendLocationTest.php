<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendLocationTest extends TestCase
{
    /**
     * @test
     */
    public function can_send_current_location()
    {
        $lat = 123;
        $lng = 123;
        $sign_type_id = 1;
        $schedule_id = 1;

        $response = $this->actingAs($this->defaultUser(),'api')
                    ->json('POST','/api/send-location',[
                        'schedule_id' => $schedule_id,
                        'lat' => $lat,
                        'lng' => $lng,
                        'sign_type' => $sign_type_id
                    ]);
        
        \Log::info($response->getContent());

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id','lat','lng','sign_type_id'
                ])
                ->assertJson([
                    'lat' => $lat,
                    'lng' => $lng,
                    'sign_type_id' => $sign_type_id
                ]);        
    }
}
