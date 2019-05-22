<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Todos;
use Faker\Generator as Faker;

$factory->define(App\Todos::class, function (Faker $faker) {
    return [
        'todo' => $faker->sentence(rand(2, 4), true),
        'deadline' => now(),
        'duedate' => now(),
        'status' => rand(0, 1),
        'userid' => 1
    ];
});
