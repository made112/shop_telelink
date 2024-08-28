<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAddressesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('addresses', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id');
			$table->string('address')->nullable();
			$table->string('country')->nullable();
			$table->string('city')->nullable();
			$table->string('postal_code')->nullable();
			$table->string('phone')->nullable();
			$table->integer('set_default')->default(0);
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
		Schema::drop('addresses');
	}

}
