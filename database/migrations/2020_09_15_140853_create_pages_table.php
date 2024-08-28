<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('title')->nullable();
			$table->string('slug')->nullable();
			$table->text('content')->nullable();
			$table->text('meta_title', 65535)->nullable();
			$table->string('meta_description', 1000)->nullable();
			$table->string('keywords', 1000)->nullable();
			$table->string('meta_image')->nullable();
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
		Schema::drop('pages');
	}

}
