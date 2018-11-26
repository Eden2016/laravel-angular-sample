<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlayerGameId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('individuals', function(Blueprint $table) {
            $table->dropColumn('primary_game_id');
            $table->integer('game_id')->unsigned()->after('steam_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('individuals', function(Blueprint $table) {
            $table->dropColumn('game_id');
            $table->integer('primary_game_id')->unsigned()->after('steam_id');
        });
    }
}
