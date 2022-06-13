<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

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
