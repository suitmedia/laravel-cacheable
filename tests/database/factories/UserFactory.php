<?php

$userClass = \Suitmedia\Cacheable\Tests\Models\User::class;

$factory->define($userClass, function (\Faker\Generator $faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->email,
        'password'       => bcrypt(str_random(12)),
        'remember_token' => str_random(12),
    ];
});
