<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeavePermissionTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('leave_permission', function (Blueprint $table) {
			$table->integer('leave_permission_id', true);
			$table->integer('employee_id');
			$table->date('leave_permission_date');
			$table->string('permission_duration', 250);
			$table->string('from_time', 100)->nullable();
			$table->string('to_time', 100)->nullable();
			$table->text('leave_permission_purpose');
			$table->integer('status')->default(1)->comment('status(1,2,3) = Pending,Approved,Reject');
			$table->boolean('manager_status')->default(1)->comment('1-pending,2-approved,3-rejected');
			$table->string('remarks')->nullable();
			$table->text('manager_remarks')->nullable();
			$table->integer('approved_by');
			$table->boolean('reject_by')->default(0);
			$table->timestamps();
			$table->date('approve_date')->nullable();
			$table->date('reject_date')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('leave_permission');
	}
}
