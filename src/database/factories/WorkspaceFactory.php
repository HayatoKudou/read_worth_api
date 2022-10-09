<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use ReadWorth\Infrastructure\EloquentModel\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkspaceFactory extends Factory
{
    public function definition()
    {
        return [
            'plan_id' => Plan::factory()->create()->id,
            'name' => Str::random(10),
        ];
    }
}
