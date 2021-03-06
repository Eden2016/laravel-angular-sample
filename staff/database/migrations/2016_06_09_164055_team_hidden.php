<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeamHidden extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_accounts', function(Blueprint $table) {
            $table->tinyInteger('hidden')->default(0)->after('active');
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
            $table->dropColumn('hidden');
        });
    }
}
