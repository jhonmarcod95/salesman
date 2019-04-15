<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransportationTest extends TestCase
{
   /**
    * @test
    */
    public function can_return_collection_of_transportation()
    {
        $response = $this->json('GET','api/transportations');
        $response->assertStatus(200)
        ->assertJsonStructure([
            [ 'id','mode' ]
        ]);
    }
}
