<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StageChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stages', function ($table) {
            $table->tinyInteger('format')->after('name');
            $table->tinyInteger('status')->after('end');
        });

        Schema::table('tournaments', function ($table) {
            $table->tinyInteger('status')->after('end');
            $table->dropColumn(['seed', 'format']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stages', function ($table) {
            $table->dropColumn(['format', 'status']);
        });

        Schema::table('tournaments', function ($table) {
            $table->tinyInteger('seed')->after('prize_distribution');
            $table->smallInteger('format')->after('name');
        });
    }
}
