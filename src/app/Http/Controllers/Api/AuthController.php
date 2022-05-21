<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController
{
    public function login(Request $request): JsonResponse
    {
        $requestPayload = $request->json();
        $email = $requestPayload->get('email');
        $password = $requestPayload->get('password');
        $user = User::where(['email' => $email])->first();

        if (null === $user) {
            return response()->json([], 401);
        }

        if (Hash::check($user->password, $password)) {
            return response()->json([], 401);
        }

        return response()->json([
            'user' => [
                'clientId'  => $user->client_id,
                'name'  => $user->name,
                'email'  => $user->email,
                'apiToken'  => $user->api_token,
            ],
            'client' => [
                'id' =>  $user->client->id,
                'name' =>  $user->client->name
            ]
        ]);
    }
}
