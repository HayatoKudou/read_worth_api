<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->text('url')->nullable()->after('image_path');
        });
    }
};
