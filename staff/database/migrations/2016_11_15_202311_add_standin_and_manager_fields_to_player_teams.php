<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStandinAndManagerFieldsToPlayerTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_teams', function (Blueprint $table) {
            $table->boolean('is_standin')->default(false);
            $table->boolean('is_manager')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('player_teams', function (Blueprint $table) {
            $table->dropColumn(['is_standin', 'is_manager']);
        });
    }
}
