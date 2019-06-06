<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Invoice;
use Faker\Generator as Faker;

$factory->define(Invoice::class, function (Faker $faker) {
    return [
        'sold_to' => $faker->name,
        'business_style' => $faker->word,
        'address' => $faker->address
    ];
});
