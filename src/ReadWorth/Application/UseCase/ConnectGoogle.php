<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\GoogleUser;
use ReadWorth\Infrastructure\EloquentModel\User;
use ReadWorth\Infrastructure\Repository\UserRepository;
use ReadWorth\Infrastructure\Repository\ConnectRepository;

class ConnectGoogle
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ConnectRepository $connectRepository,
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
