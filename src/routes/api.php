<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\FeedBackController;
use App\Http\Controllers\Api\BookReviewController;
use App\Http\Controllers\Api\BookHistoryController;
use App\Http\Controllers\Api\BookCategoryController;
use App\Http\Controllers\Api\BookRentalApplyController;
use App\Http\Controllers\Api\BookPurchaseApplyController;

Route::post('/signInGoogle', [AuthController::class, 'signInGoogle']);
Route::post('/signUpGoogle', [AuthController::class, 'signUpGoogle']);
Route::post('/feedBack/send', [FeedBackController::class, 'send']);

// TODO: verified は後で
Route::group(['prefix' => '{clientId}', 'middleware' => ['auth:api']], function (): void {
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/users', [UserController::class, 'list']);
    Route::post('/user', [UserController::class, 'create']);
    Route::put('/user', [UserController::class, 'update']);
    Route::delete('/user', [UserController::class, 'delete']);
    Route::get('/client', [ClientController::class, 'info']);
    Route::put('/client', [ClientController::class, 'update']);
    Route::get('/books', [BookController::class, 'list']);
    Route::put('/book', [BookController::class, 'update']);
    Route::post('/book', [BookController::class, 'create']);
    Route::delete('/book', [BookController::class, 'delete']);
    Route::post('/book/csvBulk', [BookController::class, 'csvBulkCreate']);
    Route::post('/bookCategory', [BookCategoryController::class, 'create']);
    Route::get('/bookPurchaseApplies', [BookPurchaseApplyController::class, 'list']);
    Route::post('/bookPurchaseApply', [BookPurchaseApplyController::class, 'create']);
    Route::post('/{bookId}/purchase/accept', [BookPurchaseApplyController::class, 'accept']);
    Route::post('/{bookId}/purchase/refuse', [BookPurchaseApplyController::class, 'refuse']);
    Route::post('/{bookId}/purchase/done', [BookPurchaseApplyController::class, 'done']);
    Route::post('/{bookId}/purchase/init', [BookPurchaseApplyController::class, 'init']);
    Route::post('/{bookId}/purchase/notification', [BookPurchaseApplyController::class, 'notification']);
    Route::post('/{bookId}/rentalApply', [BookRentalApplyController::class, 'create']);
    Route::post('/{bookId}/bookReturn', [BookController::class, 'return']);
    Route::post('/{bookId}/bookReview', [BookReviewController::class, 'create']);
    Route::get('/{bookId}/histories', [BookHistoryController::class, 'list']);
});
