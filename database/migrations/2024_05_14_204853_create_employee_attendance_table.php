<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeAttendanceTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_attendance', function (Blueprint $table) {
			$table->increments('employee_attendance_id');
			$table->string('finger_print_id', 50)->index();
			$table->integer('employee_id')->index();
			$table->text('face_id')->nullable();
			$table->integer('work_shift_id')->nullable()->index();
			$table->integer('branch_id')->nullable()->index();
			$table->string('latitude', 191)->nullable()->index();
			$table->string('longitude', 191)->nullable()->index();
			$table->string('uri', 191)->nullable()->index();
			$table->string('status', 191)->nullable()->index();
			$table->string('inout_status', 191)->nullable()->index()->comment('0-in,1-out,2-in_only');
			$table->dateTime('in_out_time')->index();
			$table->text('check_type')->nullable();
			$table->bigInteger('verify_code')->nullable()->index();
			$table->text('sensor_id')->nullable();
			$table->text('Memoinfo')->nullable();
			$table->text('WorkCode')->nullable();
			$table->text('sn')->nullable();
			$table->integer('UserExtFmt')->nullable()->index('employee_attendance_userextfmt_index');
			$table->string('mechine_sl', 20)->nullable()->index();
			$table->boolean('created_by')->nullable()->index();
			$table->boolean('updated_by')->nullable()->index();
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
		Schema::drop('employee_attendance');
	}
}
