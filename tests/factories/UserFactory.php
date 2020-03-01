<?php

use Illuminate\Foundation\Auth\User;

$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
      'name' => $faker->name,
      'email' => $faker->unique()->safeEmail,
      'password' => $password ?: $password = bcrypt('secret'),
      'remember_token' => \Illuminate\Support\Str::random(10),
    ];
});