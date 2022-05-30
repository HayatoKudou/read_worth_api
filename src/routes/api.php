<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BookCategoryController;
use App\Http\Controllers\Api\BookApplicationController;

Route::post('/signIn', [AuthController::class, 'login']);
Route::post('/signUp', [AuthController::class, 'signUp']);

Route::group(['prefix' => '{clientId}', 'middleware' => ['auth:api']], function (): void {
    Route::get('/user', [UserController::class, 'me']);
    Route::post('/user', [UserController::class, 'create']);
    Route::put('/user', [UserController::class, 'update']);
    Route::get('/users', [UserController::class, 'list']);
    Route::post('/book', [BookController::class, 'create']);
    Route::get('/books', [BookController::class, 'list']);
    Route::post('/bookCategory', [BookCategoryController::class, 'create']);
    Route::post('/bookApplication', [BookApplicationController::class, 'create']);
});
