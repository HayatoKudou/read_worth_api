<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use ReadWorth\Infrastructure\EloquentModel\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'name' => 'free',
            'price' => 0,
            'max_members' => 30,
            'max_books' => 100,
        ]);

        Plan::create([
            'name' => 'beta',
            'price' => 0,
            'max_members' => 9999,
            'max_books' => 9999,
        ]);
    }
}
