<?php

namespace Database\Factories\ReadWorth\Infrastructure\EloquentModel;

use ReadWorth\Infrastructure\EloquentModel\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        return [
            'is_account_manager' => 1,
            'is_book_manager' => 1,
            'is_workspace_manager' => 1,
        ];
    }
}
