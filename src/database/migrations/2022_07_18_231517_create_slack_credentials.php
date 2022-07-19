<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('slack_credentials', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('access_token');
            $table->string('channel_id');
            $table->string('channel_name');
            $table->timestamps();
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }
};
