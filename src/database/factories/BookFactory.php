<?php

namespace Database\Factories;

use ReadWorth\Infrastructure\EloquentModel\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookFactory extends Factory
{
    public function definition()
    {
        return [
            'workspace_id' => Workspace::factory()->make()->id,
            'book_category_id' => BookCategory::factory()->make()->id,
            'status' => Book::STATUS_CAN_LEND,
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->realText(50),
            'image_path' => '',
        ];
    }
}
