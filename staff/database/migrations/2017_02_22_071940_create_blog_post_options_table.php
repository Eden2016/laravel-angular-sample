<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogPostOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_post_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('post_id')->index()->nullable();
            $table->string('option');
            $table->text('value')->nullable();
            $table->foreign('post_id')->references('id')->on('blog_posts')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('blog_post_options');
    }
}
