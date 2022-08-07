<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlackController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\VerifyEmailController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/reset-password/{token}', function ($token) {
    return redirect()->away(config('front.url') . '/reset-password/' . $token);
})->middleware('guest')->name('password.reset');

Route::group(['prefix' => '/api/slack'], function (): void {
    Route::get('/callback', [SlackController::class, 'callback']);
});

Route::get('/connect/google', [AuthController::class, 'generateGoogleAuthUrl']);
Route::get('/connect/google-callback', [AuthController::class, 'callbackGoogleAuth']);
