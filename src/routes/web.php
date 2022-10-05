<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlackController;
use App\Http\Controllers\Api\AuthController;

Route::get('/', function () {
    return response()->json('勇者よ立ち去れ、ここには何もない');
});

Route::group(['prefix' => '/api/slack'], function (): void {
    Route::get('/{workspaceId}/connect', [SlackController::class, 'connect']);
    Route::get('/callback', [SlackController::class, 'callback']);
});

Route::get('/connect/google', [AuthController::class, 'generateGoogleAuthUrl']);
Route::get('/connect/google-callback', [AuthController::class, 'callbackGoogleAuth']);
