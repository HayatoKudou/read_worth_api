<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Str;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
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
            return response()->json(['errors' => 'パスワードが一致しません'], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json('メール認証を完了させてください', 403);
        }

        return response()->json([
            'me' => [
                'id' => $user->id,
                'clientId' => $user->client_id,
                'name' => $user->name,
                'email' => $user->email,
                'apiToken' => $user->api_token,
                'role' => [
                    'is_account_manager' => $user->role->is_account_manager,
                    'is_book_manager' => $user->role->is_book_manager,
                    'is_client_manager' => $user->role->is_client_manager,
                ],
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
            $plan = Plan::where('name', $request->get('plan'))->first();

            if (!$plan) {
                return response()->json(['一致するプランが存在しません'], 500);
            }
            $client = Client::create([
                'name' => $request->get('client_name'),
                'plan_id' => $plan->id,
            ]);
            $user = User::create([
                'client_id' => $client->id,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
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

            event(new Registered($user));

            return response()->json([
                'me' => [
                    'id' => $user->id,
                    'clientId' => $user->client_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'apiToken' => $user->api_token,
                    'role' => [
                        'is_account_manager' => $user->role->is_account_manager,
                        'is_book_manager' => $user->role->is_book_manager,
                        'is_client_manager' => $user->role->is_client_manager,
                    ],
                ],
                'client' => [
                    'id' => $user->client->id,
                    'name' => $user->client->name,
                ],
            ]);
        });
    }
}
