<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Faq;
use Faker\Factory;

class FaqsTest extends TestCase
{
    // use RefreshDatabase;

    // \Log::info($response->getContent());

    /**
     * @test
     */
    public function can_create_faq() {

        $user = 1;
        $faker = Factory::create();

        $response = $this->actingAs($this->defaultUser(), 'api')->json('POST', 'api/faqs', [
            'user_id' => $user,
            'question' => $question = $faker->sentence($nbWords = 6, $variableNbWords = true),
            'answer' => $answer = $faker->sentence($nbWords = 6, $variableNbWords = true),
        ]);

        $response
        ->assertJsonStructure([
            'id','question','answer','created_at'
        ])
        ->assertJson([
            'user_id' => $user,
            'question' => $question,
            'answer' => $answer,
        ])
        ->assertStatus(201);

        $this->assertDatabaseHas('faqs',[
            'user_id' => $user,
            'question' => $question,
            'answer' => $answer,
        ]);
    }

    /**
     * return a 404 if faq is not found
     *
     * @test
     */
    public function will_fail_with_a_404_if_faq_is_not_found() {
        $response = $this->actingAs($this->defaultUser(), 'api')->json('GET','api/faqs/-1');
        $response->assertStatus(404);
    }

    /**
     * Test can return a faq response
     *
     * @test
     */
    public function can_return_a_faq() {
            //given
            $faq = $this->create('Faq');
            // when
            $response = $this->actingAs($this->defaultUser(), 'api')->json('GET',"api/faqs/$faq->id");
            //then
            $response->assertStatus(200)
            ->assertExactJson([
                'id' => $faq->id,
                'user_id' => $faq->user_id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'created_at' => (string) $faq->created_at,
            ]);
    }

    /**
     * @test
     */
    public function can_return_a_collection_faqs() {

        $response = $this->actingAs($this->defaultUser(), 'api')->json('GET','api/faqs');

        $response->assertStatus(200)
        ->assertJsonStructure([
            [ 'id','user_id','question','answer','created_at' ]
        ]);
    }
}
