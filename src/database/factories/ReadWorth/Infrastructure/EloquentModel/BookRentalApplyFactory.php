<?php

namespace Database\Factories\ReadWorth\Infrastructure\EloquentModel;

use Carbon\Carbon;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookRentalApply;

class BookRentalApplyFactory extends Factory
{
    protected $model = BookRentalApply::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'workspace_id' => Workspace::factory()->create()->id,
            'book_id' => Book::factory()->create()->id,
            'reason' => '読みたいから',
            'rental_date' => Carbon::now(),
            'expected_return_date' => Carbon::now()->addDay(7),
            'return_date' => null,
        ];
    }
}
