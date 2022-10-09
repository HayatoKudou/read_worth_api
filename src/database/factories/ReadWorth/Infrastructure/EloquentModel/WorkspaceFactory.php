<?php

namespace Database\Factories\ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Support\Str;
use ReadWorth\Infrastructure\EloquentModel\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use ReadWorth\Infrastructure\EloquentModel\Workspace;

class WorkspaceFactory extends Factory
{
    protected $model = Workspace::class;

    public function definition()
    {
        return [
            'plan_id' => Plan::factory()->create()->id,
            'name' => Str::random(10),
        ];
    }
}
