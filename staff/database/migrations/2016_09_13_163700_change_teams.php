<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changed_teams', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->integer('original_team_id')->unsigned();
            $table->integer('substitute_team_id')->unsigned();
            $table->integer('stage_id')->unsigned();
            $table->integer('round_id')->unsigned();
            $table->tinyInteger('whole_stage');
            $table->dateTime('changed_at');

            $table->foreign('original_team_id')
              ->references('id')->on('team_accounts')
              ->onDelete('cascade');

            $table->foreign('substitute_team_id')
              ->references('id')->on('team_accounts')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('changed_teams');
    }
}
