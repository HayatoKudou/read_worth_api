<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;

Route::post('/signIn', [AuthController::class, 'login']);
Route::post('/signUp', [AuthController::class, 'signUp']);
Route::post('/client', [ClientController::class, 'create']);

Route::group(['prefix' => '{clientId}','middleware' => ['auth:api']], function () {
    Route::post('/book', [BookController::class, 'create']);
    Route::get('/books', [BookController::class, 'list']);

    Route::get('/user', [UserController::class, 'me']);
    Route::post('/user', [UserController::class, 'create']);
    Route::put('/user', [UserController::class, 'update']);
    Route::put('/users', [UserController::class, 'list']);
});
