<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ApiKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_keys', function(Blueprint $table) {
            $table->increments('id');

            $table->string('name', 80);
            $table->string('token', 32);
            $table->integer('call_quota');
            $table->smallInteger('call_cooldown');
            //$table->string('ip_ranges', 200);
            $table->string('games', 50);
            $table->string('methods', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('api_keys');
    }
}
