<?php

namespace App\Http\Controllers\Api;

use ReadWorth\Domain\GoogleUser;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use ReadWorth\Application\Service\ConnectService;
use App\Http\Response\Auth\CallbackGoogleAuthResponse;

class ConnectController extends Controller
{
    public function generateGoogleAuthUrl(): JsonResponse
    {
        $connectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response()->json(['connectUrl' => $connectUrl]);
    }

    public function callbackGoogleAuth(ConnectService $service): RedirectResponse
    {
        $connectUser = Socialite::driver('google')->stateless()->user();
        $googleUser = new GoogleUser(
            name: $connectUser->getName(),
            email: $connectUser->getEmail(),
            token: $connectUser->token,
        );
        $user = $service->callbackGoogleAuth($googleUser);

        return CallbackGoogleAuthResponse::make($user);
    }
}
