<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsStageFormatOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams_stage_format_order', function (Blueprint $table) {
            $table->integer('team_accounts_id')->index()->unsigned();
            $table->integer('stage_formats_id')->index()->unsigned();
            $table->smallInteger('pos')->default(0)->index()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('teams_stage_format_order');
    }
}
