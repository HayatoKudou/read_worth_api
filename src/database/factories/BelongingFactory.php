<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

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
