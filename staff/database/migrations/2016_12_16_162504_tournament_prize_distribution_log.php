<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TournamentPrizeDistributionLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prize_distribution_log', function (Blueprint $table) {
            $table->string('type_of')->index();
            $table->integer('action_id')->index()->unsigned();
            $table->integer('tournament_id')->index()->unsigned();
            $table->bigInteger('sum')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('prize_distribution_log');
    }
}
