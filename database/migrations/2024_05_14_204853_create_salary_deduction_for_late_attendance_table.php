<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalaryDeductionForLateAttendanceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_deduction_for_late_attendance', function(Blueprint $table)
		{
			$table->increments('salary_deduction_for_late_attendance_id');
			$table->integer('for_days');
			$table->integer('day_of_salary_deduction');
			$table->string('status', 20);
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
		Schema::drop('salary_deduction_for_late_attendance');
	}

}
