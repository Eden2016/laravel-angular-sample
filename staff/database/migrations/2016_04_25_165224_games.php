<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Games extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name', 20);
            $table->string('slug', 20);
            $table->string('hashtag', 20);
            $table->string('subreddit', 20);
            $table->smallInteger('steam_app_id')->default(0);
            $table->tinyInteger('hidden')->default(0);
        });

        Schema::table('games', function(Blueprint $table) { 
            $table->index('name');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('games');
    }
}
