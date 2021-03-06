<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanterTest extends TestCase
{
   /**
    * @test
    */
    public function check_planter_results()
    {
        $response = $this->actingAs($this->defaultUser(), 'api')
                ->json('GET','api/planters');

        $response->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);

    }

    /**
     * @test
     */
    public function check_planter_customers()
    {
        $response = $this->actingAs($this->defaultUser(), 'api')
        ->json('GET','api/planters-customers');

        $response->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @test
     */
    public function check_hacienda_details()
    {
        $response = $this->actingAs($this->defaultUser(), 'api')
        ->json('GET','api/haciendas');

        $response->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}
