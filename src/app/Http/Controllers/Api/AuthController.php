<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\PasswordSettingRequest;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
        $email = $request->get('email');
        $password = $request->get('password');
        $user = User::where(['email' => $email])->first();

        if (null === $user) {
            return response()->json(['errors' => 'ユーザーが見つかりません'], 401);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json(['errors' => 'パスワードが一致しません'], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['errors' => 'メール認証を完了させてください'], 403);
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
        ]);
    }

    public function signUp(SignUpRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request): JsonResponse {
            $plan = Plan::where('name', $request->get('plan'))->first();

            if (!$plan) {
                return response()->json(['一致するプランが存在しません'], 500);
            }
            $client = Client::create([
                'name' => $request->get('client_name'),
                'purchase_limit' => 0,
                'purchase_limit_unit' => 'monthly',
                'private_ownership_allow' => false,
                'plan_id' => $plan->id,
            ]);
            $user = User::create([
                'client_id' => $client->id,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'password_setting_at' => Carbon::now(),
                'purchase_balance' => $client->purchase_limit,
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
            ]);
        });
    }

    public function passwordSetting(PasswordSettingRequest $request): JsonResponse
    {
        $user = User::find(Auth::id());
        $user->update([
            'password' => $request->get('password'),
            'password_setting_at' => Carbon::now(),
        ]);
        return response()->json();
    }
}
