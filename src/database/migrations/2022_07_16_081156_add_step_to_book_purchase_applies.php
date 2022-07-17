<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('book_purchase_applies', function (Blueprint $table): void {
            $table->smallInteger('step')->after('price')->default(1)->after('price');
            $table->string('location')->after('step')->nullable();
        });
    }
};
