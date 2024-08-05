<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeIncrementsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_increments', function(Blueprint $table)
		{
			$table->increments('employee_increment_id');
			$table->integer('employee_id');
			$table->string('year', 4);
			$table->decimal('basic_salary', 10, 3)->default(0.000);
			$table->decimal('basic_amount', 9, 3)->default(0.000);
			$table->decimal('increment_percentage', 10, 3)->default(0.000);
			$table->decimal('increment_amount', 10, 3)->default(0.000);
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
		Schema::drop('employee_increments');
	}

}
