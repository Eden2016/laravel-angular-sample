<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeamAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_accounts', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('team_id')->unsigned();
            $table->integer('organization_id')->unsigned();
            $table->string('name', 50);
            $table->string('slug_name', 50);
            $table->string('tag', 20);
            $table->date('created');
            $table->smallInteger('region');
            $table->smallInteger('location');
            $table->string('twitter', 100)->nullable();
            $table->string('facebook', 100)->nullable();
            $table->string('steam', 100)->nullable();
            $table->string('website', 100)->nullable();
            $table->tinyInteger('active')->default(1);

            $table->timestamps();
        });

        Schema::table('team_accounts', function (Blueprint $table) {
            $table->index('team_id');
            $table->index('organization_id');
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
        Schema::drop('team_accounts');
    }
}
