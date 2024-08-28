<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductStocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_stocks', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('product_id');
			$table->string('variant')->nullable();
			$table->string('sku')->nullable();
			$table->float('price', 10)->default(0.00);
			$table->integer('qty')->default(0);
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
		Schema::drop('product_stocks');
	}

}
