<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'client_id' => 1,
            'book_category_id' => 1,
            'status' => Book::STATUS_CAN_LEND,
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->realText(50),
            'image_path' => '',
        ];
    }
}
