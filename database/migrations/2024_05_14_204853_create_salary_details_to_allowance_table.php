<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalaryDetailsToAllowanceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_details_to_allowance', function(Blueprint $table)
		{
			$table->increments('salary_details_to_allowance_id');
			$table->integer('salary_details_id');
			$table->integer('allowance_id');
			$table->integer('amount_of_allowance');
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
		Schema::drop('salary_details_to_allowance');
	}

}
