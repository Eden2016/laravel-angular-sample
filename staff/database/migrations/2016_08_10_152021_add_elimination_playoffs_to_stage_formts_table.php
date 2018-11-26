<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEliminationPlayoffsToStageFormtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stage_formats', function (Blueprint $table) {
            $table->enum('elimination_playoffs', ['single', 'double'])->nullable();
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
            $table->dropColumn('elimination_playoffs');
        });
    }
}
