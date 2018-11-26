<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Standins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_standins', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('individual_id')->unsigned();
            $table->integer('match_game_id')->unsigned();
            $table->integer('team_id')->unsigned();

            $table->foreign('individual_id')
                ->references('id')
                ->on('individuals')
                ->onDelete('cascade');

            $table->foreign('match_game_id')
                ->references('id')
                ->on('match_games')
                ->onDelete('cascade');

            $table->foreign('team_id')
                ->references('id')
                ->on('team_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('match_standins');
    }
}
