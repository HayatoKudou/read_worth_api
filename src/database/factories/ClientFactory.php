<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition()
    {
        return [
            'plan_id' => Plan::factory()->make(['name' => 'test'])->id,
            'name' => $this->faker->name,
        ];
    }
}
