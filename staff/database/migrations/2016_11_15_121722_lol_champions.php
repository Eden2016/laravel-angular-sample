<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LolChampions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lol_champions', function(Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('name', 25);
            $table->string('slug_name', 30);
            $table->string('title', 100);
            $table->integer('api_id');
            $table->string('image', 30);
            $table->text('info');
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
        Schema::dropTable('lol_champions');
    }
}
