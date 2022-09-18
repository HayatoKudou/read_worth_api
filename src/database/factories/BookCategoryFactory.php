<?php

namespace Database\Factories;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookCategoryFactory extends Factory
{
    public function definition()
    {
        return [
            'workspace_id' => Workspace::factory()->make()->id,
            'name' => 1,
        ];
    }
}
