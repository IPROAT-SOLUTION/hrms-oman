<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePayGradeToAllowanceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pay_grade_to_allowance', function(Blueprint $table)
		{
			$table->increments('pay_grade_to_allowance_id');
			$table->integer('pay_grade_id');
			$table->integer('allowance_id');
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
		Schema::drop('pay_grade_to_allowance');
	}

}
