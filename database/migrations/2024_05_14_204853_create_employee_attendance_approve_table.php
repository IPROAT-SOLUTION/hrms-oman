<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeAttendanceApproveTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_attendance_approve', function(Blueprint $table)
		{
			$table->increments('employee_attendance_approve_id');
			$table->integer('employee_id')->index();
			$table->integer('finger_print_id')->index();
			$table->integer('branch_id')->nullable()->index();
			$table->date('date')->index();
			$table->string('in_time', 191)->index();
			$table->string('out_time', 191)->index();
			$table->string('working_hour', 191)->index();
			$table->string('approve_working_hour', 191)->index();
			$table->integer('created_by');
			$table->integer('updated_by');
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
		Schema::drop('employee_attendance_approve');
	}

}
