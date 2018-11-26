<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlayerTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_teams', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->integer('individual_id')->unsigned();
            $table->integer('team_id')->unsigned();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->tinyInteger('is_coach');
        });

        Schema::table('player_teams', function (Blueprint $table) {
            $table->index('individual_id');
            $table->index('team_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('player_teams');
    }
}
