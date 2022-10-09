<?php

namespace ReadWorth\Application\Service;

use ReadWorth\Domain\GoogleUser;
use ReadWorth\Infrastructure\Repository\IUserRepository;
use ReadWorth\Infrastructure\Repository\IConnectRepository;
use ReadWorth\Infrastructure\EloquentModel\User;

class ConnectService
{
    public function __construct(
        private readonly IUserRepository $userRepository,
        private readonly IConnectRepository $connectRepository,
    ) {
    }

    public function callbackGoogleAuth(GoogleUser $googleUser): User
    {
        $user = $this->userRepository->getByEmail($googleUser->getEmail());

        if ($user) {
            $this->userRepository->updateGoogleAccessToken($user, $googleUser->getToken());
            return $user;
        }
        return $this->connectRepository->store($googleUser);
    }
}
