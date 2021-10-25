<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Articles;
use Faker\Generator as Faker;

$factory->define(Articles::class, function (Faker $faker) {
    return [
        'author_id' => '1',
        'title' => $faker->title,
        'content' => $faker->paragraph
    ];
});
