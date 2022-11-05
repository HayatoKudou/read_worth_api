<?php

namespace Database\Factories\ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Database\Eloquent\Factories\Factory;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookCategoryFactory extends Factory
{
    protected $model = BookCategory::class;

    public function definition()
    {
        return [
            'workspace_id' => Workspace::factory()->create()->id,
            'name' => 'IT',
        ];
    }
}
