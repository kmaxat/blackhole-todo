<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/
use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Models\Color::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->colorName,
        'hex_string' => $faker->hexColor
    ];
});

$factory->define(App\Models\Task::class, function (Faker\Generator $faker) {
    return [
        'description' => $faker->text(200),
        'priority' => $faker->randomElement([1,2,3,4]),
        'due_at' => Carbon::today()
                        ->addWeek($faker->numberBetween(1, 10))
                        ->toDateTimeString(),
        'user_id' => 1,
        'status' => $faker->randomElement(['archived','completed', 'deleted', null]),
        'project_id' => null
    ];
});

$factory->define(App\Models\Project::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->streetName,
        'color_id' => $faker->numberBetween(1, 12),
        'user_id' => 1,
        'status' => $faker->randomElement(['archived', 'deleted', null]),
    ];
});
$factory->define(App\Models\Label::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'color_id' => $faker->numberBetween(1, 12),
    ];
});
