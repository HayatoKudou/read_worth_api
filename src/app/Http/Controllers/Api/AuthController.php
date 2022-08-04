<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\SignInRequestEmail;
use App\Http\Requests\Auth\SignUpRequestEmail;
use App\Http\Requests\Auth\SignInGoogleRequest;
use App\Http\Requests\Auth\SignUpGoogleRequest;
use App\Http\Requests\Auth\PasswordSettingRequest;

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

    public function signInGoogle(SignInGoogleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::where(['email' => $validated['email']])->first();

        if (null === $user || $user->google_access_token !== $validated->accessToken) {
            return response()->json(['errors' => ['custom' => '認証に失敗しました']], 401);
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
            $user = User::create([
                'client_id' => null,
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

            return response()->json(['userId' => $user->id]);
        });
    }

    public function signUpGoogle(SignUpGoogleRequest $request)
    {
        $validated = $request->validated();
        return DB::transaction(function () use ($validated): JsonResponse {
            $user = User::create([
                'email' => $validated['email'],
                'name' => $validated['name'],
                'google_access_token' => $validated['accessToken'],
                'purchase_balance' => 0,
                'api_token' => Str::random(60),
            ]);
            Role::create([
                'user_id' => $user->id,
                'is_account_manager' => 1,
                'is_book_manager' => 1,
                'is_client_manager' => 1,
            ]);

            return response()->json(['userId' => $user->id]);
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
