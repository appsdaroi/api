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
        Schema::create('betano_saques', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('transicao_id', 40)->nullable();
            $table->dateTime('data');
            $table->string('remetente')->default('OKTO PAGAMENTOS S.A');
            $table->float('valor');
            $table->string('tipo');
            $table->float('saldo_atual_betano');
            $table->float('saldo_atual_nubank');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('betano_saques');
    }
};
