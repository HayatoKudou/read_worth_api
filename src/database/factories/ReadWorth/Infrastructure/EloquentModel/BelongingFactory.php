<?php

namespace Database\Factories\ReadWorth\Infrastructure\EloquentModel;

use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;

class BelongingFactory extends Factory
{
    protected $model = Belonging::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'workspace_id' => Workspace::factory()->create()->id,
            'role_id' => Role::factory()->create()->id,
        ];
    }
}
