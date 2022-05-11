<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::post('/signUp', [UserController::class, 'create']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
