<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LiveMatchesSeriesType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_matches', function (Blueprint $table) {
            $table->tinyInteger('series_type')->after('stage');
            $table->tinyInteger('game_number')->after('series_type');
            $table->tinyInteger('series_id')->after('game_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('live_matches', function (Blueprint $table) {
            $table->dropColumn('series_type');
            $table->dropColumn('game_number');
            $table->dropColumn('series_id');
        });
    }
}
