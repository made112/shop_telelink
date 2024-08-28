<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('name', 65535);
			$table->string('added_by', 6)->default('admin');
			$table->integer('user_id');
			$table->integer('category_id');
			$table->integer('subcategory_id');
			$table->integer('subsubcategory_id')->nullable();
			$table->integer('brand_id')->nullable();
			$table->string('photos', 2000)->nullable();
			$table->string('thumbnail_img', 100)->nullable();
			$table->string('featured_img', 100)->nullable();
			$table->string('flash_deal_img', 100)->nullable();
			$table->string('video_provider', 20)->nullable();
			$table->string('video_link', 100)->nullable();
			$table->text('tags', 16777215)->nullable();
			$table->text('description')->nullable();
			$table->float('unit_price');
			$table->float('purchase_price');
			$table->integer('variant_product')->default(0);
			$table->string('attributes', 1000)->default('[]');
			$table->text('choice_options', 16777215)->nullable();
			$table->text('colors', 16777215)->nullable();
			$table->text('variations', 65535)->nullable();
			$table->integer('todays_deal')->default(0);
			$table->integer('published')->default(1);
			$table->integer('featured')->default(0);
			$table->integer('current_stock')->default(0);
			$table->text('unit', 65535)->nullable();
			$table->integer('min_qty')->nullable()->default(1);
			$table->float('discount')->nullable();
			$table->string('discount_type', 10)->nullable();
			$table->float('tax')->nullable();
			$table->string('tax_type', 10)->nullable();
			$table->string('shipping_type', 20)->nullable()->default('flat_rate');
			$table->float('shipping_cost')->nullable()->default(0.00);
			$table->integer('num_of_sale')->default(0);
			$table->text('meta_title', 16777215)->nullable();
			$table->text('meta_description')->nullable();
			$table->string('meta_img')->nullable();
			$table->string('pdf')->nullable();
			$table->text('slug', 16777215);
			$table->float('rating')->default(0.00);
			$table->string('barcode')->nullable();
			$table->integer('digital')->default(0);
			$table->string('file_name')->nullable();
			$table->string('file_path')->nullable();
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
		Schema::drop('products');
	}

}
