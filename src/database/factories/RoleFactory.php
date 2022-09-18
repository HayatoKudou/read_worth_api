<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory()->make()->id,
            'is_account_manager' => 1,
            'is_book_manager' => 1,
            'is_workspace_manager' => 1,
        ];
    }
}
