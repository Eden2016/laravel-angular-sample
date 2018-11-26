<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ToutouMatchesTypo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('toutou_matches', function(Blueprint $table) {
            $table->renameColumn('competiton_name', 'competition_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('toutou_matches', function(Blueprint $table) {
            $table->renameColumn('competition_name', 'competiton_name');
        });
    }
}
