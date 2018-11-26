<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWalkoverWinFieldToMatchGames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_games', function (Blueprint $table) {
            $table->boolean('walkover')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('match_games', function (Blueprint $table) {
            $table->dropColumn('walkover');
        });
    }
}
