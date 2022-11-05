<?php

namespace ReadWorth\UI\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use ReadWorth\Domain\Entities\GoogleUser;
use ReadWorth\Application\UseCase\ConnectGoogle;
use ReadWorth\UI\Http\Responders\CallbackGoogleAuthResponse;

class ConnectController
{
    public function generateGoogleAuthUrl(): JsonResponse
    {
        $connectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response()->json(['connectUrl' => $connectUrl]);
    }

    public function callbackGoogleAuth(ConnectGoogle $useCase): RedirectResponse
    {
        $connectUser = Socialite::driver('google')->stateless()->user();
        $googleUser = new GoogleUser(
            name: $connectUser->getName(),
            email: $connectUser->getEmail(),
            token: $connectUser->token,
        );
        $user = $useCase->callbackGoogleAuth($googleUser);

        return CallbackGoogleAuthResponse::make($user);
    }
}
