<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maps', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('game_id')->index()->default(1);
            $table->string('name', 255);
            $table->string('image', 255)->nullable();
            $table->enum('status', ['active_duty', 'reserve_group'])->default('reserve_group')->index();
            $table->enum('type', ['bomb', 'hostage'])->default('bomb')->index();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('maps');
    }
}
