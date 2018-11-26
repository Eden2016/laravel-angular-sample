<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StageFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stage_formats', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('stage_id');
            $table->string('name', 50);
            $table->tinyInteger('type');
            $table->tinyInteger('number');
            $table->tinyInteger('hidden');
            $table->tinyInteger('active');
            $table->tinyInteger('status');

            $table->timestamps();

            $table->index('stage_id');
        });

        Schema::table('stage_rounds', function (Blueprint $table) {
            $table->renameColumn('stage_id', 'stage_format_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stage_rounds', function (Blueprint $table) {
            $table->renameColumn('stage_format_id', 'stage_id');
        });

        Schema::drop('stage_formats');
    }
}
