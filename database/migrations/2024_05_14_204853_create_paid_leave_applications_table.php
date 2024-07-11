<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaidLeaveApplicationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('paid_leave_applications', function (Blueprint $table) {
			$table->increments('paid_leave_application_id');
			$table->integer('leave_type_id')->unsigned();
			$table->integer('employee_id')->unsigned();
			$table->date('application_from_date');
			$table->date('application_to_date');
			$table->date('application_date');
			$table->integer('number_of_day');
			$table->date('approve_date')->nullable();
			$table->date('reject_date')->nullable();
			$table->integer('approve_by')->nullable();
			$table->integer('reject_by')->nullable();
			$table->text('purpose');
			$table->text('remarks')->nullable();
			$table->string('status', 191)->default('1')->comment('status(1,2,3) = Pending,Approve,Reject');
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
		Schema::drop('paid_leave_applications');
	}
}
