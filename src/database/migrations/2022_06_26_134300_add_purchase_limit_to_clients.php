<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('purchase_limit')->after('name');
            $table->string('purchase_limit_unit', 20)->after('purchase_limit')->default('monthly');
        });
    }
};
