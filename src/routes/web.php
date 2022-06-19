<?php

use Illuminate\Support\Facades\Route;
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
