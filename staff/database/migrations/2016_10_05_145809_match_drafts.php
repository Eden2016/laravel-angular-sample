<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MatchDrafts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_drafts', function(Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('dummy_match_id')->unsigned();
            $table->text('draft');

            $table->foreign('dummy_match_id')
                ->references('id')
                ->on('dummy_matches')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('match_drafts');
    }
}
