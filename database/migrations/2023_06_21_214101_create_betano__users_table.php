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
        Schema::create('betano_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('cpf')->nullable();
            $table->float('saldo_betano')->default(0);
            $table->float('saldo_nubank')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('betano_users');
    }
};
