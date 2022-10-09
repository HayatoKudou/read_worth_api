<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => 'test',
            'price' => 1000,
            'max_members' => 10,
            'max_books' => 10,
        ];
    }
}
