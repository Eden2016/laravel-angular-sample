<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Organizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name', 50);
            $table->string('slug_name', 50);
            $table->string('short_handle', 20);
            $table->date('created');
            $table->smallInteger('region');
            $table->smallInteger('country');
            $table->string('ceo', 50);
            $table->string('manager', 50);
            $table->text('bio');
            $table->text('sareholders');
            $table->string('twitter', 100);
            $table->string('facebook', 100);
            $table->string('website', 100);
            $table->string('instagram', 100);
            $table->string('youtube', 100);
            $table->string('vk', 100);
            $table->string('twitch', 100);
            $table->string('steam', 100);
            $table->tinyInteger('active')->default(1);

            $table->timestamps();
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->index('slug_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('organizations');
    }
}
