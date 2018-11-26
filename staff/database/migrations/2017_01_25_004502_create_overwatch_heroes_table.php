<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOverwatchHeroesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overwatch_heroes', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 35);
            $table->string('slug_name', 30);
            $table->string('image', 60);
            $table->text('info');
            $table->smallInteger('role');
            $table->tinyInteger('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('overwatch_heroes');
    }
}
