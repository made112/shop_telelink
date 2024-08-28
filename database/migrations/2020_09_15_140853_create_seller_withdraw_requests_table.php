<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSellerWithdrawRequestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('seller_withdraw_requests', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->nullable();
			$table->float('amount')->nullable();
			$table->text('message')->nullable();
			$table->integer('status')->nullable();
			$table->integer('viewed')->nullable();
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
		Schema::drop('seller_withdraw_requests');
	}

}
