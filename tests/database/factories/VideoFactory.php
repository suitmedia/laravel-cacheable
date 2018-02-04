<?php

$videoClass = \Suitmedia\Cacheable\Tests\Models\Video::class;

$factory->define($videoClass, function (\Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'url'   => $faker->url,
    ];
});
