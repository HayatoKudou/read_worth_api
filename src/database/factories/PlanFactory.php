<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class PlanFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => "test",
            'price' => 1000,
            'max_members' => 10,
            'max_books' => 10,
        ];
    }
}
