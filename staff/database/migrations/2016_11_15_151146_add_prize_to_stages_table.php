<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrizeToStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->integer('prize')->nullable();
            $table->tinyInteger('currency')->nullable();
            $table->text('prize_distribution')->nullable();
            $table->tinyInteger('prize_dist_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropColumn(['prize', 'currency', 'prize_distribution', 'prize_dist_type']);
        });
    }
}
