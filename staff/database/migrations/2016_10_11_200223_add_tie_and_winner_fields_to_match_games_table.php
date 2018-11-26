<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTieAndWinnerFieldsToMatchGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_games', function (Blueprint $table) {
            $table->boolean('is_tie')->default(false)->index();
            $table->integer('winner')->nullable()->index();
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
            $table->dropColumn(['is_tie', 'winner']);
        });
    }
}
