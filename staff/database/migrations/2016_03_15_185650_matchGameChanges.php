<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MatchGameChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_games', function (Blueprint $table) {
            $table->tinyInteger('is_crawled')->default(0);

            $table->bigInteger('match_id')->nullable()->unsigned()->change();
            $table->integer('dummy_match_id')->unsigned()->change();

            $table->index('match_id');
            $table->index('dummy_match_id');
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
        Schema::table('match_games', function (Blueprint $table) {
            $table->dropColumn('is_crawled');

            $table->bigInteger('match_id')->change();

            $table->dropIndex('match_games_match_id_index');
            $table->dropIndex('match_games_dummy_match_id_index');

            $table->dropForeign('match_games_dummy_match_id_foreign');
        });
    }
}
