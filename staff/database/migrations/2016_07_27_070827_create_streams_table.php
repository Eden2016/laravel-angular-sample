<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('title', 500)->nullable();
            $table->string('link', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('lang', 2)->default('en')->index();
        });

        Schema::create('events_streams', function(Blueprint $table){
           $table->integer('events_id')->unsigned()->index();
            $table->integer('streams_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('streams');
        Schema::drop('events_streams');
    }
}
