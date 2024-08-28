<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubSubCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sub_sub_categories', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('sub_category_id')->index('fk_sub_category_id');
			$table->text('name', 65535);
			$table->string('slug')->nullable();
			$table->text('meta_title', 65535)->nullable();
			$table->text('meta_description', 65535)->nullable();
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
		Schema::drop('sub_sub_categories');
	}

}
