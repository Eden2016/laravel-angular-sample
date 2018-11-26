<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DummyMatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dummy_matches', function (Blueprint $table) {
            $table->increments('id');

            $table->bigInteger('match_id')->nullable();
            $table->integer('round_id');
            $table->string('opponent1', 50)->nullable();
            $table->string('opponent2', 50)->nullable();
            $table->integer('winner')->nullable();
            $table->dateTime('start')->index()->nullable();
            $table->tinyInteger('status');
            $table->tinyInteger('hidden')->default(0);

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
        Schema::drop('dummy_matches');
    }
}
