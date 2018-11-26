<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sc2Races extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sc2_player_races', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('individual_id')->unsigned();
            $table->smallInteger('race');

            $table->foreign('individual_id')
                ->references('id')->on('individuals')
                ->onDelete('cascade');
        });

        Schema::table('individuals', function(Blueprint $table) {
            $table->dropColumn('sc2_race');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sc2_player_races');

        Schema::table('individuals', function(Blueprint $table) {
            $table->tinyInteger('sc2_race')->after('player_role')->default(0);
        });
    }
}
