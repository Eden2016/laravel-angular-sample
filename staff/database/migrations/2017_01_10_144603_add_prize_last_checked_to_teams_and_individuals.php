<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrizeLastCheckedToTeamsAndIndividuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('individuals', function (Blueprint $table) {
            $table->date('last_prizes_check')->nullable();
        });
        Schema::table('team_accounts', function (Blueprint $table) {
            $table->date('last_prizes_check')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('individuals', function (Blueprint $table) {
            $table->dropColumn('last_prizes_check');
        });
        Schema::table('team_accounts', function (Blueprint $table) {
            $table->dropColumn('last_prizes_check');
        });
    }
}
