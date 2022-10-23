<?php

namespace ReadWorth\Domain;

use ReadWorth\Infrastructure\EloquentModel\User;

interface IUserRepository
{
    public function getByEmail(string $email): User|null;

    public function updateGoogleAccessToken(User $user, string $token);
}
