<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 50)->comment('プラン名');
            $table->smallInteger('price')->comment('プラン価格');
            $table->smallInteger('max_members')->comment('メンバー上限数');
            $table->smallInteger('max_books')->comment('書籍上限数');
            $table->timestamps();
        });
    }
};
