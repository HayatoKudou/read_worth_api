<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Client;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition()
    {
        return [
            'plan_id' => Plan::factory()->make(['name' => "test"])->id,
            'name' => $this->faker->name,
            'enable_purchase_limit' => true,
            'purchase_limit' => 1000,
            'purchase_limit_unit' => 'monthly',
            'private_ownership_allow' => true,
        ];
    }
}
