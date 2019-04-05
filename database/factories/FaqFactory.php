<?php

use Faker\Generator as Faker;

$factory->define(App\Faq::class, function (Faker $faker) {
    $question = $faker->sentence($nbWords = 6, $variableNbWords = true);
    $answer = $faker->sentence($nbWords = 6, $variableNbWords = true);
    return [
        'user_id' => 1,
        'question' => $question,
        'answer' => $answer,
    ];
});
