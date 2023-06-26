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
        Schema::table('igmoney_users', function (Blueprint $table) {
            $table->enum('banco', ['itau', 'nubank'])->nullable()->after('user_id');
        });

        \App\Models\IGMoney_User::query()->update([
            'banco' => 'itau',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('igmoney_users', function (Blueprint $table) {
            $table->dropColumn('banco');
        });
    }
};
