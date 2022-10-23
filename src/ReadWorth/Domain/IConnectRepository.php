<?php

namespace ReadWorth\Domain;

use ReadWorth\Domain\Entities\GoogleUser;
use ReadWorth\Infrastructure\EloquentModel\User;

interface IConnectRepository
{
    public function store(GoogleUser $googleUser): User;
}
