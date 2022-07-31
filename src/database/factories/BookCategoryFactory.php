<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookCategoryFactory extends Factory
{
    public function definition()
    {
        return [
            'client_id' => Client::factory()->make()->id,
            'name' => 1,
        ];
    }
}
