<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisqualifiedTeamFieldToDummyMatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dummy_matches', function (Blueprint $table) {
            $table->integer('disqualified_team')->nullable();
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
            $table->dropColumn('disqualified_team');
        });
    }
}
