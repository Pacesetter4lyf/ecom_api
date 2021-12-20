<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('item_sold')->nullable();
            $table->string('current_price');
            $table->string('former_price')->nullable();
            $table->string('color');
            $table->string('short_description');
            $table->string('long_description');
            $table->string('category');
            $table->string('tags');
            $table->string('features');
            $table->string('image');
            $table->string('sm_images_id')->nullable();
            $table->string('size')->nullable();
            $table->string('is_featured');
            $table->string('is_latest');
            $table->string('is_unique');
            $table->string('is_trending');
            $table->string('discount');
            $table->string('brand');
            $table->string('code')->nullable();
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
        Schema::dropIfExists('products');
    }
}
