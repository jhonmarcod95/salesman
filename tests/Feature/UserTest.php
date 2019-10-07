<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * @test     *
     */
    public function check_user_approver()
    {
        $response = $this->actingAs($this->defaultUser(),'api')->json('GET','api/users/approver');

        \Log::info($response->getContent());

        $response->assertStatus(200);

        echo "\n\n".json_encode($response,JSON_PRETTY_PRINT);

    }
}
