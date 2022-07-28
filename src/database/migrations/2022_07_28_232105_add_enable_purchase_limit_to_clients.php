<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->boolean('enable_purchase_limit')->after('name');
        });
    }
};
