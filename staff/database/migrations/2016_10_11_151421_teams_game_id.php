<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeamsGameId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_accounts', function(Blueprint $table) {
            $table->integer('game_id')->unsigned()->default(1)->after('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_accounts', function(Blueprint $table) {
            $table->dropColumn('game_id');
        });
    }
}
