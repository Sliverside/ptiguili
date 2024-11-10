<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('__temp_won_gifts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('gift_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'requested', 'used', 'sold'])->default('pending');
        });

        DB::statement('INSERT INTO __temp_won_gifts SELECT * FROM won_gifts');

        Schema::dropIfExists('won_gifts');

        Schema::rename('__temp_won_gifts','won_gifts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('__temp_won_gifts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('gift_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'requested', 'used'])->default('pending');
        });

        DB::statement('INSERT INTO __temp_won_gifts SELECT * FROM won_gifts');

        Schema::dropIfExists('won_gifts');

        Schema::rename('__temp_won_gifts','won_gifts');
    }
};
