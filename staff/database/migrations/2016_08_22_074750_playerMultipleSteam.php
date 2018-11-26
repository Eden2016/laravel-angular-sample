<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlayerMultipleSteam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('individual_steam_id', function (Blueprint $table) {
            $table->integer('individual_id')->unsigned();
            $table->bigInteger('steam_id')->unsigned();
        });

        Schema::table('individual_steam_id', function (Blueprint $table) {
            $table->foreign('individual_id')
              ->references('id')->on('individuals')
              ->onDelete('cascade');

            $table->index('steam_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('individual_steam_id');
    }
}
