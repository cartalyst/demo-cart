<?php

use Illuminate\Database\Migrations\Migration;

class CreateCartsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('carts', function($table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('instance');
			$table->timestamps();
		});

		Schema::create('carts_items', function($table)
		{
			$table->increments('id');
			$table->integer('cart_id');
			$table->integer('product_id');
			$table->integer('quantity');
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
		Schema::drop('carts_items');
	}

}
