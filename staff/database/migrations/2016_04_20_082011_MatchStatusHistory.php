<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MatchStatusHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_status_history', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->bigInteger('match_id');
            $table->integer('time');
            $table->smallInteger('duration');
            $table->smallInteger('tower_status_radiant');
            $table->smallInteger('tower_status_dire');
            $table->smallInteger('barracks_status_radiant');
            $table->smallInteger('barracks_status_dire');
            $table->tinyInteger('score_radiant');
            $table->tinyInteger('score_dire');
        });

        Schema::table('match_status_history', function(Blueprint $table) { 
            $table->index('match_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('match_status_history');
    }
}
