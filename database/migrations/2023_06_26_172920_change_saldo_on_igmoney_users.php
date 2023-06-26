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
        $users = \App\Models\IGMoney_User::query()->get()->values()->toArray();

        Schema::table('igmoney_users', function (Blueprint $table) {
            $table->dropColumn('saldo');
        });

        Schema::table('igmoney_users', function (Blueprint $table) {
            $table->bigInteger('saldo')->default(0)->after('user_id');
        });

        foreach ($users as $user) {
            \App\Models\IGMoney_User::query()->where('id', $user['id'])->update([
                'saldo' => $user['saldo'],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $users = \App\Models\IGMoney_User::query()->get()->values()->toArray();

        Schema::table('igmoney_users', function (Blueprint $table) {
            $table->dropColumn('saldo');
        });

        Schema::table('igmoney_users', function (Blueprint $table) {
            $table->integer('saldo')->default(0)->after('user_id');
        });

        foreach ($users as $user) {
            \App\Models\IGMoney_User::query()->where('id', $user['id'])->update([
                'saldo' => $user['saldo'],
            ]);
        }
    }
};
