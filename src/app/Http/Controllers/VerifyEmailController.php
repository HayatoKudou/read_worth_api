<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VerifyEmailController extends Controller
{
    public function verify(Request $request): RedirectResponse
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return redirect()->away(config('front.url'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        return redirect()->away(config('front.url'));
    }

    public function resendVerify(Request $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->get('email'))->firstOrFail();
            $user->sendEmailVerificationNotification();
            return response()->json([], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('一致するユーザーが見つかりません。', 500);
        }
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        Password::sendResetLink(
            $request->only('email')
        );
        return response()->json();
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
        Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );
        return response()->json();
    }
}
