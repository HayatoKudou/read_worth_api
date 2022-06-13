<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->unsignedBigInteger('plan_id')->after('id');
            $table->foreign('plan_id')->references('id')->on('plans');
        });
    }
};
