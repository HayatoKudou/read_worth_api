<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;

Route::post('/signUp', [UserController::class, 'create']);
Route::post('/signUp', [UserController::class, 'create']);
Route::post('/createClient', [ClientController::class, 'create']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/main/list','MainController@list')->name('main.list');
    Route::get('/main/edit','MainController@edit')->name('main.edit');
    Route::get('/main/search','MainController@main')->name('main.search');
});
