<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make($this->faker->password),
            'password_setting_at' => now(),
            'purchase_balance' => 1000,
            'remember_token' => Str::random(10),
            'api_token' => Str::random(60),
        ];
    }
}
