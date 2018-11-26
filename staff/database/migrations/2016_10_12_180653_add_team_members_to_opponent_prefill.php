<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTeamMembersToOpponentPrefill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opponent_prefill', function (Blueprint $table) {
            $table->string('team_members')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opponent_prefill', function (Blueprint $table) {
            $table->dropColumn('[]');
        });
    }
}
