<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\GoogleUser;
use ReadWorth\Infrastructure\EloquentModel\User;

interface IConnectRepository
{
    public function store(GoogleUser $googleUser): User;
}
