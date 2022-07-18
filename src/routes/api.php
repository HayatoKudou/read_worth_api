<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\Api\BookReviewController;
use App\Http\Controllers\Api\BookHistoryController;
use App\Http\Controllers\Api\BookCategoryController;
use App\Http\Controllers\Api\BookRentalApplyController;
use App\Http\Controllers\Api\BookPurchaseApplyController;

Route::post('/signIn', [AuthController::class, 'login']);
Route::post('/signUp', [AuthController::class, 'signUp']);
Route::post('/email/verify/resend', [VerifyEmailController::class, 'resendVerify']);
Route::post('/forgot-password', [VerifyEmailController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset-password', [VerifyEmailController::class, 'resetPassword'])->name('password.update');
Route::post('/password-setting', [AuthController::class, 'passwordSetting']);

Route::group(['prefix' => '{clientId}', 'middleware' => ['auth:api', 'verified']], function (): void {
    Route::get('/user', [UserController::class, 'me']);
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
    Route::post('/{bookId}/purchase/allow', [BookPurchaseApplyController::class, 'allow']);
    Route::post('/{bookId}/purchase/reject', [BookPurchaseApplyController::class, 'reject']);
    Route::post('/{bookId}/purchase/done', [BookPurchaseApplyController::class, 'done']);
    Route::post('/{bookId}/purchase/init', [BookPurchaseApplyController::class, 'init']);
    Route::post('/{bookId}/rentalApply', [BookRentalApplyController::class, 'create']);
    Route::post('/{bookId}/bookReturn', [BookController::class, 'return']);
    Route::post('/{bookId}/bookReview', [BookReviewController::class, 'create']);
    Route::get('/{bookId}/histories', [BookHistoryController::class, 'list']);
});
