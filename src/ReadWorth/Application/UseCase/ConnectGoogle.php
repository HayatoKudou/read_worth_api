<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\GoogleUser;
use ReadWorth\Domain\IUserRepository;
use ReadWorth\Domain\IConnectRepository;
use ReadWorth\Infrastructure\EloquentModel\User;

class ConnectGoogle
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
