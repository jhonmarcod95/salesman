<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BrandTest extends TestCase
{
   /**
    * @test
    */
    public function can_view_brands()
    {
        $response = $this->actingAs($this->defaultUser(),'api')
                        ->json('GET','/api/brands');

        $response->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}
