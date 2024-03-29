<?php

namespace Database\Factories\ReadWorth\Infrastructure\EloquentModel;

use ReadWorth\Domain\ValueObjects\BookStatus;
use ReadWorth\Infrastructure\EloquentModel\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition()
    {
        return [
            'workspace_id' => Workspace::factory()->create()->id,
            'book_category_id' => BookCategory::factory()->create()->id,
            'status' => BookStatus::STATUS_CAN_LEND,
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->realText(50),
            'image_path' => '',
            'url' => '',
        ];
    }
}
