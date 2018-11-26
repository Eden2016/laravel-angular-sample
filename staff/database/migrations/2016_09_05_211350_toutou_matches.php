<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ToutouMatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('toutou_matches', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('competition_id')->unsigned();
            $table->string('competiton_name', 100)->nullable();
            $table->tinyInteger('competition_no')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->integer('parent_event')->unsigned()->nullable();
            $table->integer('dummy_match')->unsigned()->nullable();
            $table->bigInteger('event_date')->unsigned();
            $table->text('odds');
            $table->text('new_odds');
            $table->integer('home_team');
            $table->integer('away_team');
            $table->smallInteger('home_score')->unsigned()->default(0);
            $table->smallInteger('away_score')->unsigned()->default(0);
            $table->tinyInteger('in_play')->default(0);
            $table->tinyInteger('automatic_assigment')->default(0);
            $table->tinyInteger('active')->default(1);

            $table->timestamps();

            $table->foreign('dummy_match')
                ->references('id')->on('dummy_matches');
        });

        /*Schema::create('toutou_to_dummy_matches', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('dummy_match_id')->unsigned();
            $table->integer('toutou_match_id')->unsigned();

            $table->foreign('dummy_match_id')
                ->references('id')->on('dummy_matches');

            $table->foreign('toutou_match_id')
                ->references('id')->on('toutou_matches');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('toutou_matches');
    }
}
