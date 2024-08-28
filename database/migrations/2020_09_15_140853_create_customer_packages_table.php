<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerPackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer_packages', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name')->nullable();
			$table->float('amount', 28)->nullable();
			$table->integer('product_upload')->nullable();
			$table->string('logo', 150)->nullable();
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
		Schema::drop('customer_packages');
	}

}
