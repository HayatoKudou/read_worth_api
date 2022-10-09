<?php

namespace S4T\Application\Service;

use ReadWorth\Domain\GoogleUser;

class AuthService
{
    public function callbackGoogleAuth(GoogleUser $googleUser): void
    {
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update(['google_access_token' => $googleUser->token]);
            return CallbackGoogleAuthResponse::make($user);
        }
    }
}
