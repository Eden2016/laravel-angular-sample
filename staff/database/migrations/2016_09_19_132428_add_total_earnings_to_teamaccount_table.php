<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalEarningsToTeamaccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_accounts', function (Blueprint $table) {
            $table->bigInteger('total_earnings')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_accounts', function (Blueprint $table) {
            $table->dropColumn('total_earnings');
        });
    }
}
