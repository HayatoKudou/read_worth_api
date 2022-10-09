<?php

namespace Database\Factories\ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Support\Str;
use ReadWorth\Infrastructure\EloquentModel\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'google_access_token' => Str::random(60),
            'api_token' => Str::random(60),
        ];
    }
}
