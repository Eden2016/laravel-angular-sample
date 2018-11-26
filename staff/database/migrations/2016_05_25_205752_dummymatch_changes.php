<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DummymatchChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dummy_matches', function (Blueprint $table) {
            $table->tinyInteger('is_tie')->default(0)->after('status');
            $table->tinyInteger('is_forfeited')->default(0)->after('is_tie');
            $table->tinyInteger('done')->default(0)->after('is_forfeited');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dummy_matches', function (Blueprint $table) {
            $table->dropColumn('is_tie');
            $table->dropColumn('is_forfeited');
            $table->dropColumn('done');
        });
    }
}
