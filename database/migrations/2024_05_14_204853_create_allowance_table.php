<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAllowanceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('allowance', function(Blueprint $table)
		{
			$table->increments('allowance_id');
			$table->string('allowance_name', 250);
			$table->string('allowance_type', 100);
			$table->integer('percentage_of_basic');
			$table->integer('limit_per_month');
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
		Schema::drop('allowance');
	}

}
