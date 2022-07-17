<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('book_rental_applies', function (Blueprint $table): void {
            $table->dropColumn('review');
        });
    }
};
