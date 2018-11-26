<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ToutouMatchesGameNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('toutou_matches', function(Blueprint $table) {
            $table->smallInteger('game_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('toutou_matches', function(Blueprint $table) {
            $table->dropColumn('game_number');
        });
    }
}
