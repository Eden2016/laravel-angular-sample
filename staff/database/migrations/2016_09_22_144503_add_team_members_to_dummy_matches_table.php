<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTeamMembersToDummyMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dummy_matches', function (Blueprint $table) {
            $table->string('opponent1_members', 255)->nullable();
            $table->string('opponent2_members', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dummy_matches', function (Blueprint $table) {
            $table->dropColumn(['opponent1_members', 'opponent2_members']);
        });
    }
}
