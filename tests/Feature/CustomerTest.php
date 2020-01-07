<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    /**
     * @test
     */
    public function check_customer_from_request_it()
    {
        $response = $this->actingAs($this->defaultUser(),'api')
                ->json('GET','api/customers');
        
        $response->assertStatus(200);
        
        echo "\n\n".json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @test
     */
    public function view_customer_detail()
    {   
        $customer_code = 120900005756;

        $response = $this->actingAs($this->defaultUser(),'api')
                    ->json('GET','api/customer/'.$customer_code);

        $response->assertStatus(200);

        echo "\n\n".json_encode($response, JSON_PRETTY_PRINT);
    }
}
