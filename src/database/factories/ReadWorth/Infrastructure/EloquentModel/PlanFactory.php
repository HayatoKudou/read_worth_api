<?php

namespace Database\Factories\ReadWorth\Infrastructure\EloquentModel;

use ReadWorth\Infrastructure\EloquentModel\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

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
