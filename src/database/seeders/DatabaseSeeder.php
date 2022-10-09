<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use ReadWorth\Infrastructure\EloquentModel\Book;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
//                PlanSeeder::class,
                Book::factory()->count(1)->create(),
            ]
        );
    }
}
