<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Str;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\SignUpRequest;

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
                'clientId' => $user->client_id,
                'name' => $user->name,
                'email' => $user->email,
                'apiToken' => $user->api_token,
            ],
            'client' => [
                'id' => $user->client->id,
                'name' => $user->client->name,
            ],
        ]);
    }

    public function signUp(SignUpRequest $request): JsonResponse
    {
        $request->validated();
        return DB::transaction(function () use ($request): JsonResponse {
            $client = Client::create([
                'name' => $request->client_name,
            ]);
            $user = User::create([
                'client_id' => $client->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'api_token' => Str::random(60),
            ]);
            Role::create([
                'user_id' => $user->id,
                'is_account_manager' => 1,
                'is_book_manager' => 1,
                'is_client_manager' => 1,
            ]);
            BookCategory::create([
                'client_id' => $client->id,
                'name' => 'ALL',
            ]);
            return response()->json([
                'me' => [
                    'clientId' => $user->client_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'apiToken' => $user->api_token,
                ],
                'client' => [
                    'id' => $user->client->id,
                    'name' => $user->client->name,
                ],
            ]);
        });
    }
}
