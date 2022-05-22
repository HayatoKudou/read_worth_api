<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;

Route::post('/signIn', [AuthController::class, 'login']);
Route::post('/signUp', [AuthController::class, 'signUp']);
Route::post('/createClient', [ClientController::class, 'create']);

Route::group(['prefix' => '{clientId}','middleware' => ['auth:api']], function () {
    Route::prefix('book')->group(function (): void {
        Route::get('/list', [BookController::class, 'list']);
        Route::post('/register', [BookController::class, 'create']);
    });
    Route::prefix('user')->group(function (): void {
        Route::get('/me', [UserController::class, 'me']);
        Route::get('/list', [UserController::class, 'list']);
        Route::post('/create', [UserController::class, 'create']);
    });
});
