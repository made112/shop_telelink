<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePickupPointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pickup_points', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('staff_id');
			$table->string('name');
			$table->text('address', 65535);
			$table->string('phone', 15);
			$table->integer('pick_up_status')->nullable();
			$table->integer('cash_on_pickup_status')->nullable();
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
		Schema::drop('pickup_points');
	}

}
