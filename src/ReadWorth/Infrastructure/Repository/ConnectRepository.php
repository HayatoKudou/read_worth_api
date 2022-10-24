<?php

namespace ReadWorth\Infrastructure\Repository;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use ReadWorth\Domain\IConnectRepository;
use ReadWorth\Domain\Entities\GoogleUser;
use ReadWorth\Infrastructure\EloquentModel\Plan;
use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class ConnectRepository implements IConnectRepository
{
    public function store(GoogleUser $googleUser): User
    {
        return DB::transaction(function () use ($googleUser) {
            $plan = Plan::where('name', 'free')->first();
            $workspace = Workspace::create([
                'name' => uniqid(),
                'plan_id' => $plan->id,
            ]);
            BookCategory::create([
                'workspace_id' => $workspace->id,
                'name' => 'ALL',
            ]);
            $user = User::create([
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'google_access_token' => $googleUser->getToken(),
                'api_token' => Str::random(60),
            ]);
            $role = Role::create([
                'is_account_manager' => 1,
                'is_book_manager' => 1,
                'is_workspace_manager' => 1,
            ]);
            Belonging::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'role_id' => $role->id,
            ]);
            return $user;
        });
    }
}
