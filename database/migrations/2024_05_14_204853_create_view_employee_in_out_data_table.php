<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateViewEmployeeInOutDataTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('view_employee_in_out_data', function (Blueprint $table) {
			$table->bigInteger('employee_attendance_id', true)->unsigned();
			$table->integer('branch_id')->nullable()->default(1)->index();
			$table->integer('approve_over_time_id')->nullable()->index();
			$table->integer('incentive_details_id')->nullable()->index();
			$table->integer('comp_off_details_id')->nullable()->index();
			$table->string('finger_print_id', 50)->index();
			$table->date('date')->nullable()->index();
			$table->dateTime('in_time')->nullable()->index();
			$table->dateTime('out_time')->nullable()->index();
			$table->time('working_time')->nullable()->index();
			$table->time('working_hour')->nullable()->index();
			$table->time('over_time')->nullable()->index();
			$table->time('approved_over_time')->nullable();
			$table->time('early_by')->nullable()->index();
			$table->time('late_by')->nullable()->index();
			$table->text('in_out_time')->nullable();
			$table->string('shift_name', 150)->nullable()->index();
			$table->integer('work_shift_id')->nullable()->index();
			$table->string('device_name', 150)->nullable()->index();
			$table->string('inout_status', 191)->nullable()->index();
			$table->boolean('live_status')->nullable()->default(0)->index();
			$table->boolean('attendance_status')->nullable()->index();
			$table->boolean('status')->nullable()->default(1)->index();
			$table->integer('created_by')->nullable()->index();
			$table->integer('updated_by')->nullable()->index();
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
		Schema::drop('view_employee_in_out_data');
	}
}
