<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ToutouOddsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('toutou_matches', function (Blueprint $table) {
            $table->text('odds_hk')->after('new_odds');
            $table->text('new_odds_hk')->after('odds_hk');

            $table->text('odds_malay')->after('new_odds_hk');
            $table->text('new_odds_malay')->after('odds_malay');

            $table->text('odds_indo')->after('new_odds_malay');
            $table->text('new_odds_indo')->after('odds_indo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('toutou_matches', function (Blueprint $table) {
            $table->dropColumn('odds_hk');
            $table->dropColumn('new_odds_hk');

            $table->dropColumn('odds_malay');
            $table->dropColumn('new_odds_malay');

            $table->dropColumn('odds_indo');
            $table->dropColumn('new_odds_indo');
        });
    }
}
