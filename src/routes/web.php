<?php

use Illuminate\Support\Facades\Route;
use ReadWorth\UI\Http\Controllers\SlackController;
use ReadWorth\UI\Http\Controllers\ConnectController;

Route::get('/', function () {
    return response()->json('勇者よ立ち去れ、ここには何もない');
});

Route::group(['prefix' => '/api/slack'], function (): void {
    Route::get('/{workspaceId}/connect', [SlackController::class, 'connect']);
    Route::get('/callback', [SlackController::class, 'callback']);
});

Route::get('/connect/google', [ConnectController::class, 'generateGoogleAuthUrl']);
Route::get('/connect/google-callback', [ConnectController::class, 'callbackGoogleAuth']);
Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});
