<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Foreign key constraint for stage_formats -> stages
        Schema::table('stage_formats', function(Blueprint $table) {
            $table->integer('stage_id')->unsigned()->change();
        });
        Schema::table('stage_formats', function (Blueprint $table) {
            $table->foreign('stage_id')->references('id')->on('stages')->onDelete('cascade');
        });

        //Foreign key constraint for stage_rounds -> stage_formats
        Schema::table('stage_rounds', function(Blueprint $table) {
            $table->integer('stage_format_id')->unsigned()->change();
        });
        Schema::table('stage_rounds', function (Blueprint $table) {
            $table->foreign('stage_format_id')->references('id')->on('stage_formats')->onDelete('cascade');
        });

        //Foreign key constraint for dummy_matches -> stage_rounds
        Schema::table('dummy_matches', function(Blueprint $table) {
            $table->integer('round_id')->unsigned()->change();
        });
        Schema::table('dummy_matches', function (Blueprint $table) {
            $table->foreign('round_id')->references('id')->on('stage_rounds')->onDelete('cascade');
        });

        //Foreign key constraint for match_games -> dummy_matches
        Schema::table('match_games', function(Blueprint $table) {
            $table->integer('round_id')->unsigned()->change();
        });
        Schema::table('match_games', function (Blueprint $table) {
            $table->foreign('dummy_match_id')->references('id')->on('dummy_matches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Delete foreign key constraint match_games -> dummy_matches
        Schema::table('match_games', function (Blueprint $table) {
            $table->dropForeign('match_games_dummy_match_id_foreign');
        });

        //Delete foreign key constraint dummy_matches -> stage_rounds
        Schema::table('dummy_matches', function (Blueprint $table) {
            $table->dropForeign('dummy_matches_round_id_foreign');
        });

        //Delete foreign key constraint stage_rounds -> stage_formats
        Schema::table('stage_rounds', function (Blueprint $table) {
            $table->dropForeign('stage_rounds_stage_format_id_foreign');
        });

        //Delete foreign key constraint stage_formats -> stages
        Schema::table('stage_formats', function (Blueprint $table) {
            $table->dropForeign('stage_formats_stage_id_foreign');
        });
    }
}
