<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangedTeamsChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('changed_teams');

        Schema::create('changed_teams', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->integer('original_team_id')->unsigned();
            $table->integer('substitute_team_id')->unsigned();
            $table->integer('stage_format_id')->unsigned();
            $table->integer('match_id')->unsigned();
            $table->tinyInteger('whole_sf');
            $table->dateTime('added_at');

            $table->foreign('original_team_id')
              ->references('id')->on('team_accounts')
              ->onDelete('cascade');

            $table->foreign('substitute_team_id')
              ->references('id')->on('team_accounts')
              ->onDelete('cascade');

            $table->foreign('stage_format_id')
              ->references('id')->on('stage_formats')
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
