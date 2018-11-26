<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MatchTeamsId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dummy_matches', function (Blueprint $table) {
            $table->integer('opponent1')->unsigned()->change();
            $table->foreign('opponent1')->references('id')->on('team_accounts');

            $table->integer('opponent2')->unsigned()->change();
            $table->foreign('opponent2')->references('id')->on('team_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dummy_matches', function (Blueprint $table) {
            $table->dropForeign('dummy_matches_opponent1_foreign');
            $table->string('opponent1', 50)->change();

            $table->dropForeign('dummy_matches_opponent2_foreign');
            $table->string('opponent2', 50)->change();
        });
    }
}
