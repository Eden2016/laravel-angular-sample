<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SlotsIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('slots', function (Blueprint $table) {
            $table->index('match_id');
            $table->index('account_id');
            $table->index('hero_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('slots', function (Blueprint $table) {
            $table->dropIndex('slots_match_id_index');
            $table->dropIndex('slots_account_id_index');
            $table->dropIndex('slots_hero_id_index');
        });
    }
}
