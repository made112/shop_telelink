<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsoredProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsored_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id');
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->integer('start_date')->nullable();
            $table->integer('end_date')->nullable();
            $table->integer('status')->default(0);
            $table->integer('featured')->default(0);
            $table->string('banner')->nullable();
            $table->string('slug')->nullable();
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
        Schema::dropIfExists('sponsored_products');
    }
}
