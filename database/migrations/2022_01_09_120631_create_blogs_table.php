<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('banner');
            $table->string('banner_aux')->nullable();
            $table->string('title');
            $table->string('short_desc');
            $table->string('category');
            $table->string('is_recent');
            $table->string('tags');
            $table->longText('content');
            $table->string('special_text');
            $table->string('static_img');
            $table->string('video_img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blogs');
    }
}
