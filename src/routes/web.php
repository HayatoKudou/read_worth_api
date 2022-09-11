<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlackController;
use App\Http\Controllers\Api\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => '/api/slack'], function (): void {
    Route::get('/{clientId}/connect', [SlackController::class, 'connect']);
    Route::get('/callback', [SlackController::class, 'callback']);
});

Route::get('/connect/google', [AuthController::class, 'generateGoogleAuthUrl']);
Route::get('/connect/google-callback', [AuthController::class, 'callbackGoogleAuth']);
