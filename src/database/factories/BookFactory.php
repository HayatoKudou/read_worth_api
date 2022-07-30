<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition()
    {
        return [
            'client_id' => Client::factory()->make()->id,
            'book_category_id' => BookCategory::factory()->make()->id,
            'status' => Book::STATUS_CAN_LEND,
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->realText(50),
            'image_path' => '',
        ];
    }
}
