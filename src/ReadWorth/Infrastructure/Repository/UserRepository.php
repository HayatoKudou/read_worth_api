<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Infrastructure\EloquentModel\User;

class UserRepository
{
    public function getByEmail(string $email): User|null
    {
        return User::where('email', $email)->first();
    }

    public function updateGoogleAccessToken(User $user, string $token): void
    {
        $user->update(['google_access_token' => $token]);
    }
}
