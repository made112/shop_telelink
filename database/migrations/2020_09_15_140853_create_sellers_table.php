<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSellersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sellers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id');
			$table->integer('verification_status')->default(0);
			$table->text('verification_info')->nullable();
			$table->integer('cash_on_delivery_status')->default(0);
			$table->float('admin_to_pay')->default(0.00);
			$table->string('bank_name')->nullable();
			$table->string('bank_acc_name', 200)->nullable();
			$table->string('bank_acc_no', 50)->nullable();
			$table->integer('bank_routing_no')->nullable();
			$table->integer('bank_payment_status')->default(0);
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
		Schema::drop('sellers');
	}

}
