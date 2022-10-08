<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Support\Str;
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
