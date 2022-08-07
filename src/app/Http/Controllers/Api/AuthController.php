<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Str;
use App\Models\BookCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\Auth\SignInRequestEmail;
use App\Http\Requests\Auth\SignUpRequestEmail;
use App\Http\Requests\Auth\PasswordSettingRequest;
use App\Http\Response\Auth\CallbackGoogleAuthResponse;

class AuthController
{
    public function signInEmail(SignInRequestEmail $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::where(['email' => $validated['email']])->first();

        if (null === $user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['errors' => ['custom' => 'メールアドレスもしくはパスワードが一致しません']], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['errors' => ['custom' => 'メール認証を完了させてください']], 403);
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

    public function signUpEmail(SignUpRequestEmail $request): JsonResponse
    {
        $validated = $request->validated();
        return DB::transaction(function () use ($validated): JsonResponse {
            $plan = Plan::where('name', 'free')->first();
            $client = Client::create([
                'name' => uniqid(),
                'enable_purchase_limit' => false,
                'purchase_limit' => 0,
                'purchase_limit_unit' => 'monthly',
                'private_ownership_allow' => false,
                'plan_id' => $plan->id,
            ]);
            BookCategory::create([
                'client_id' => $client->id,
                'name' => 'ALL',
            ]);
            $user = User::create([
                'client_id' => $client->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'password_setting_at' => Carbon::now(),
                'purchase_balance' => 0,
                'api_token' => Str::random(60),
            ]);
            Role::create([
                'user_id' => $user->id,
                'is_account_manager' => 1,
                'is_book_manager' => 1,
                'is_client_manager' => 1,
            ]);
            event(new Registered($user));

            return response()->json();
        });
    }

    public function generateGoogleAuthUrl(): JsonResponse
    {
        $connectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response()->json(['connectUrl' => $connectUrl]);
    }

    public function callbackGoogleAuth(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            return CallbackGoogleAuthResponse::make($user);
        }

        return DB::transaction(function () use ($googleUser) {
            $plan = Plan::where('name', 'free')->first();
            $client = Client::create([
                'name' => uniqid(),
                'enable_purchase_limit' => false,
                'purchase_limit' => 0,
                'purchase_limit_unit' => 'monthly',
                'private_ownership_allow' => false,
                'plan_id' => $plan->id,
            ]);
            BookCategory::create([
                'client_id' => $client->id,
                'name' => 'ALL',
            ]);
            $user = User::create([
                'client_id' => $client->id,
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'email_verified_at' => now(),
                'google_access_token' => $googleUser->token,
                'purchase_balance' => 0,
                'api_token' => Str::random(60),
            ]);
            Role::create([
                'user_id' => $user->id,
                'is_account_manager' => 1,
                'is_book_manager' => 1,
                'is_client_manager' => 1,
            ]);

            return CallbackGoogleAuthResponse::make($user);
        });
    }

    public function passwordSetting(PasswordSettingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        User::find(Auth::id())->update([
            'password' => $validated['password'],
            'password_setting_at' => Carbon::now(),
        ]);
        return response()->json();
    }
}
