<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCartsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('carts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable();
			$table->integer('product_id')->nullable();
			$table->text('variation', 65535)->nullable();
			$table->float('price')->nullable();
			$table->float('tax')->nullable();
			$table->float('shipping_cost')->nullable();
			$table->integer('quantity')->default(0);
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
		Schema::drop('carts');
	}

}
