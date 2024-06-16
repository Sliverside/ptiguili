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
            $table->unsignedDecimal('probability', 4, 1)->default(100)->change();
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
            $table->unsignedDecimal('probability', 4, 1)->default(null)->change();
        });
    }
};
