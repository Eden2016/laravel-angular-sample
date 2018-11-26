<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BetTeamGameId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('188bet_teams', function(Blueprint $table) {
            $table->smallInteger('game_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('188bet_teams', function(Blueprint $table) {
            $table->dropColumn('game_id');
        });
    }
}
