<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalaryDetailsToDeductionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_details_to_deduction', function(Blueprint $table)
		{
			$table->increments('salary_details_to_deduction_id');
			$table->integer('salary_details_id');
			$table->integer('deduction_id');
			$table->integer('amount_of_deduction');
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
		Schema::drop('salary_details_to_deduction');
	}

}
