<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('book_purchase_applies', function (Blueprint $table) {
            $table->smallInteger('step')->after('price')->default(0);
        });
    }
};
