<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('book_rental_applies', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('book_id');
            $table->text('reason')->comment('申請理由');
            $table->text('review')->comment('レビュー')->nullable();
            $table->date('rental_date')->comment('貸出日');
            $table->date('expected_return_date')->comment('返却予定日');
            $table->date('return_date')->comment('返却日')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('book_id')->references('id')->on('books');
        });
    }
};
