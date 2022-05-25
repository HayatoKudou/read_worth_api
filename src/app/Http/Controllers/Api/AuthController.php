<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\SignUpRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    public function signUp(SignUpRequest $request): JsonResponse
    {
        $validated = $request->store();
        return DB::transaction(function () use ($validated): JsonResponse {
            $client = Client::create([
                'name' => $validated["client_name"],
            ]);
            $user = User::create([
                'client_id' => $client->id,
                'name' => $validated["name"],
                'email' => $validated["email"],
                'password' => Hash::make($validated["password"]),
                'api_token' => Str::random(60)
            ]);
            Role::create([
                'user_id' => $user->id,
                'is_account_manager' => 1,
                'is_book_manager' => 1,
                'is_client_manager' => 1,
            ]);
            return response()->json([
                'me' => [
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
        });
    }
}
