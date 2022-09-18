<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\Belonging;
use App\Models\Workspace;
use Illuminate\Support\Str;
use App\Models\BookCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Response\Auth\CallbackGoogleAuthResponse;

class AuthController
{
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
            $user->update(['google_access_token' => $googleUser->token]);
            return CallbackGoogleAuthResponse::make($user);
        }

        return DB::transaction(function () use ($googleUser) {
            $plan = Plan::where('name', 'free')->first();
            $workspace = Workspace::create([
                'name' => uniqid(),
                'plan_id' => $plan->id,
            ]);
            BookCategory::create([
                'workspace_id' => $workspace->id,
                'name' => 'ALL',
            ]);
            $user = User::create([
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'google_access_token' => $googleUser->token,
                'api_token' => Str::random(60),
            ]);
            $role = Role::create([
                'is_account_manager' => 1,
                'is_book_manager' => 1,
                'is_workspace_manager' => 1,
            ]);
            Belonging::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'role_id' => $role->id,
            ]);
            return CallbackGoogleAuthResponse::make($user);
        });
    }
}
