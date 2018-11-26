<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndividualChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('individuals', function (Blueprint $table){
            $table->bigInteger('steam_id')->nullable()->change();
            $table->string('nickname', 50)->nullable()->change();
            $table->string('slug_nick')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->smallInteger('nationality')->nullable()->change();
            $table->smallInteger('location')->nullable()->change();
            $table->text('bio')->nullable()->change();
            $table->string('twitter', 100)->nullable()->change();
            $table->string('facebook', 100)->nullable()->change();
            $table->string('twitch', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('individuals', function (Blueprint $table){
            $table->bigInteger('steam_id')->change();
            $table->string('nickname', 50)->change();
            $table->string('slug_nick')->change();
            $table->string('first_name')->change();
            $table->string('last_name')->change();
            $table->date('date_of_birth')->change();
            $table->smallInteger('nationality')->change();
            $table->smallInteger('location')->change();
            $table->text('bio')->change();
            $table->string('twitter', 100)->change();
            $table->string('facebook', 100)->change();
            $table->string('twitch', 100)->change();
        });
    }
}
