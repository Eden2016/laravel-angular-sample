<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChampionPickBans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lol_champion_picks', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('match_game_id')->unsigned();
            $table->integer('player_id')->unsigned();
            $table->integer('champion_id')->unsigned();

            $table->foreign('match_game_id')
                  ->references('id')->on('match_games')
                  ->onDelete('cascade');

            $table->foreign('player_id')
                  ->references('id')->on('individuals');

            $table->foreign('champion_id')
                  ->references('id')->on('lol_champions');
        });

        Schema::create('lol_champion_bans', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('match_game_id')->unsigned();
            $table->integer('team_id')->unsigned();
            $table->integer('champion_id')->unsigned();

            $table->foreign('match_game_id')
                  ->references('id')->on('match_games')
                  ->onDelete('cascade');

            $table->foreign('team_id')
                  ->references('id')->on('team_accounts');

            $table->foreign('champion_id')
                  ->references('id')->on('lol_champions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lol_champion_picks');
        Schema::drop('lol_champion_bans');
    }
}
