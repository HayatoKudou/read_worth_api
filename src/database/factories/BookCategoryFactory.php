<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use ReadWorth\Infrastructure\EloquentModel\Workspace;

class BookCategoryFactory extends Factory
{
    public function definition()
    {
        return [
            'workspace_id' => Workspace::factory()->make()->id,
            'name' => 1,
        ];
    }
}
