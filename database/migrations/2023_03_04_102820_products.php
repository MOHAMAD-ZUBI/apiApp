<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('bio');
            $table->string('img_in_page');
            $table->string('img_profile');
            $table->string('file_exe');
            $table->string('file_souce_code');
            $table->string('status')->default("Working");
            $table->integer('count_prushed')->default(0);
            $table->integer('price_7day')->default(0);
            $table->integer('price_30day')->default(0);
            $table->integer('price_lifetime')->default(0);
            $table->integer('price_source_code')->default(0);
            $table->integer('create_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};