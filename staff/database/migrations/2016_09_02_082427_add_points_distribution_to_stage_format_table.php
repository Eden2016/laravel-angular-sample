<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPointsDistributionToStageFormatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stage_formats', function (Blueprint $table) {

            $table->smallInteger('points_per_win')->unsigned()->default(0);
            $table->smallInteger('points_per_draw')->unsigned()->default(0);
            $table->enum('points_distribution', ['per_match', 'per_game'])->default('per_match');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stage_formats', function (Blueprint $table) {
            $table->dropColumn(['points_per_win', 'points_per_draw', 'points_distribution']);
        });
    }
}
