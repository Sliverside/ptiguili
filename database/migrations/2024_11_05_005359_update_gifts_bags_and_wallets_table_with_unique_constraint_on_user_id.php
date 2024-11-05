<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gifts_bags', function (Blueprint $table) {
            $table->unique('user_id', 'gifts_bags_user_id_is_unique');
        });
        Schema::table('wallets', function (Blueprint $table) {
            $table->unique('user_id', 'wallets_user_id_is_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gifts_bags', function (Blueprint $table) {
            $table->dropUnique('gifts_bags_user_id_is_unique');
        });
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropUnique('wallets_user_id_is_unique');
        });
    }
};
