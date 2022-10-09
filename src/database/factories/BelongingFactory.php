<?php

namespace Database\Factories;

use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use ReadWorth\Infrastructure\EloquentModel\Workspace;

class BelongingFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'workspace_id' => Workspace::factory()->create()->id,
            'role_id' => Role::factory()->create()->id,
        ];
    }
}
