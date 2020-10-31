<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanterHaciendaTest extends TestCase
{
    /**
     * @test
     */
    public function search_hacienda_planters()
    {
        $response = $this->actingAs($this->defaultUser(),'api')
                        ->json('POST','api/haciendas/search');

        $response->assertStatus(200);

        echo json_encode($response, JSON_PRETTY_PRINT);
        
    }
}
