<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->integer('purchase_limit')->after('name');
            $table->string('purchase_limit_unit', 20)->after('purchase_limit')->default('monthly');
        });
    }
};
