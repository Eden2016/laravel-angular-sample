<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Individuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('individuals', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->bigInteger('steam_id');
            $table->integer('primary_game_id')->unsigned();
            $table->string('nickname', 50)->null();
            $table->string('slug_nick')->null();
            $table->string('first_name')->null();
            $table->string('last_name')->null();
            $table->date('date_of_birth');
            $table->smallInteger('nationality');
            $table->smallInteger('location');
            $table->text('bio');
            $table->string('twitter', 100)->null();
            $table->string('facebook', 100)->null();
            $table->string('twitch', 100)->null();
            $table->tinyInteger('active')->default(1);

            $table->timestamps();
        });

        Schema::table('individuals', function (Blueprint $table) {
            $table->index('steam_id');
            $table->index('slug_nick');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('individuals');
    }
}
