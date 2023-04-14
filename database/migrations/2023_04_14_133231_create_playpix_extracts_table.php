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
        Schema::create('playpix_extracts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('quotes');
            $table->integer('value');
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playpix_extracts');
    }
};
