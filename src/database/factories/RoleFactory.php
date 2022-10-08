<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition()
    {
        return [
            'is_account_manager' => 1,
            'is_book_manager' => 1,
            'is_workspace_manager' => 1,
        ];
    }
}
