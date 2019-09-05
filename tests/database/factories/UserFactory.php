<?php

use Illuminate\Support\Str;

$userClass = \Suitmedia\Cacheable\Tests\Models\User::class;

$factory->define($userClass, function (\Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(Str::random(12)),
        'remember_token' => Str::random(12),
    ];
});
