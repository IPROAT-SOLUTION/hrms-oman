<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeOvertimeTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_overtime', function (Blueprint $table) {
			$table->increments('employee_over_time_id');
			$table->string('date', 191)->index();
			$table->text('employee_id');
			$table->text('work_shift_id');
			$table->string('Overtime_duration', 191)->index('employee_overtime_overtime_duration_index');
			$table->boolean('status')->nullable()->default(1)->index();
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
		Schema::drop('employee_overtime');
	}
}
