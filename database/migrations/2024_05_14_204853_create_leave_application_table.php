<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaveApplicationTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('leave_application', function (Blueprint $table) {
			$table->increments('leave_application_id');
			$table->integer('employee_id')->unsigned()->index();
			$table->integer('branch_id')->nullable()->index();
			$table->integer('leave_type_id')->unsigned()->index();
			$table->date('application_from_date')->index();
			$table->date('application_to_date')->index();
			$table->date('application_date')->index();
			$table->float('number_of_day', 5)->index();
			$table->date('approve_date')->nullable()->index();
			$table->date('reject_date')->nullable()->index();
			$table->integer('approve_by')->nullable()->index();
			$table->integer('reject_by')->nullable()->index();
			$table->text('purpose');
			$table->text('remarks')->nullable();
			$table->text('manager_remarks')->nullable();
			$table->string('status', 191)->default('1')->index()->comment('status(1,2,3) = Pending,Approve,Reject');
			$table->boolean('manager_status')->default(1)->comment('1-pending,2approved,3-rejected
');
			$table->string('document')->nullable();
			$table->timestamps();
			$table->integer('created_by')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('leave_application');
	}
}
