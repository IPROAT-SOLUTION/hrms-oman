<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePayGradeToDeductionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pay_grade_to_deduction', function(Blueprint $table)
		{
			$table->increments('pay_grade_to_deduction_id');
			$table->integer('pay_grade_id');
			$table->integer('deduction_id');
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
		Schema::drop('pay_grade_to_deduction');
	}

}
