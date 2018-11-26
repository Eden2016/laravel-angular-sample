<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EventGames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_games', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->integer('game_id')->unsigned();
        });

        Schema::table('event_games', function (Blueprint $table) {
            $table->foreign('event_id')
              ->references('id')->on('events')
              ->onDelete('cascade');

            $table->foreign('game_id')
              ->references('id')->on('games')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_games');
    }
}
