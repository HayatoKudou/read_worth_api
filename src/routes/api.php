<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FeedBackController;
use App\Http\Controllers\Api\WorkSpaceController;
use ReadWorth\UI\Http\Controllers\BookController;
use App\Http\Controllers\Api\BookReviewController;
use App\Http\Controllers\Api\BookRentalApplyController;
use ReadWorth\UI\Http\Controllers\BookHistoryController;
use App\Http\Controllers\Api\BookPurchaseApplyController;
use ReadWorth\UI\Http\Controllers\BookCategoryController;

Route::post('/feedBack/send', [FeedBackController::class, 'send']);

Route::group(['prefix' => '{workspaceId}', 'middleware' => ['auth:api']], function (): void {
    Route::get('/me', [UserController::class, 'me']);
    Route::put('/me', [UserController::class, 'meUpdate']);
    Route::post('/user', [UserController::class, 'create']);
    Route::put('/user', [UserController::class, 'update']);
    Route::get('/users', [UserController::class, 'list']);
    Route::delete('/user', [UserController::class, 'delete']);
    Route::get('/workspace', [WorkSpaceController::class, 'info']);
    Route::put('/workspace', [WorkSpaceController::class, 'update']);
    Route::post('/workspace', [WorkSpaceController::class, 'create']);
    Route::get('/workspaces', [WorkSpaceController::class, 'list']);
    Route::get('/books', [BookController::class, 'list']);
    Route::put('/book', [BookController::class, 'update']);
    Route::post('/book', [BookController::class, 'create']);
    Route::delete('/book', [BookController::class, 'delete']);
    Route::post('/book/csvBulk', [BookController::class, 'csvBulkCreate']);
    Route::post('/bookCategory', [BookCategoryController::class, 'create']);
    Route::delete('/bookCategory', [BookCategoryController::class, 'delete']);
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
