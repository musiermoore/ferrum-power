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
            $table->unsignedBigInteger('category_id')
                ->default(1); // main category
            $table->string('title')->unique();
            $table->string('slug')->unique();

            $table->integer('price_for_one')->default(0);
            $table->boolean('stock_availability');
            $table->string('description')->nullable();

            $table->string('image_path')
                ->default('default.png'); // no image

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('category_products');
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
