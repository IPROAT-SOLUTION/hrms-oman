<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeEducationQualificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_education_qualification', function(Blueprint $table)
		{
			$table->increments('employee_education_qualification_id');
			$table->integer('employee_id')->unsigned();
			$table->string('institute', 200);
			$table->string('board_university', 200);
			$table->string('degree', 200);
			$table->string('result', 100)->nullable();
			$table->string('cgpa', 50)->nullable();
			$table->date('passing_year');
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
		Schema::drop('employee_education_qualification');
	}

}
