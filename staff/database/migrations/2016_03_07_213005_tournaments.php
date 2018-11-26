<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Tournaments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->string('name');
            $table->smallInteger('format');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->tinyInteger('season');
            $table->integer('prize');
            $table->text('prize_distribution');
            $table->tinyInteger('seed');
            $table->text('description');
            $table->tinyInteger('hidden');
            $table->tinyInteger('active');

            $table->foreign('event_id')->references('id')->on('events');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tournaments');
    }
}
