<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OpponentPrefill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opponent_prefill', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('stage_format_id')->unsigned();
            $table->integer('opponent_id')->unsigned();
        });

        Schema::table('opponent_prefill', function (Blueprint $table) {
            $table->foreign('stage_format_id')->references('id')->on('stage_formats');
            $table->foreign('opponent_id')->references('id')->on('team_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('opponent_prefill');
    }
}
