<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveLeadIndicatorFromTournamentToStageFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stage_formats', function (Blueprint $table) {
            $table->boolean('lead_from_winner_bracket')->default(false);
        });
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('lead_from_winner_bracket');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stage_formats', function (Blueprint $table) {
            $table->dropColumn('lead_from_winner_bracket');
        });
        Schema::table('tournaments', function (Blueprint $table) {
            $table->boolean('lead_from_winner_bracket')->default(false);
        });
    }
}
