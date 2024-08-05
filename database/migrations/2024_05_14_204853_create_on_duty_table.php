<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOnDutyTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('on_duty', function (Blueprint $table) {
			$table->bigInteger('on_duty_id', true)->unsigned();
			$table->string('employee_id');
			$table->date('application_from_date');
			$table->date('application_to_date');
			$table->date('application_date');
			$table->float('no_of_days', 10);
			$table->text('purpose')->nullable();
			$table->text('remark_admin')->nullable();
			$table->text('remark_superadmin')->nullable();
			$table->boolean('status')->default(1);
			$table->boolean('manager_status')->default(1);
			$table->boolean('hr_status')->default(1);
			$table->boolean('is_work_from_home')->default(0);
			$table->integer('accepted_admin')->nullable();
			$table->integer('rejected_admin')->nullable();
			$table->integer('accepted_superadmin')->nullable();
			$table->integer('rejected_superadmin')->nullable();
			$table->timestamps();
			$table->string('head_remarks')->nullable();
			$table->integer('primary_approval')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('on_duty');
	}
}
