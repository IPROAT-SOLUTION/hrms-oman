<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee', function (Blueprint $table) {
			$table->increments('employee_id');
			$table->integer('user_id')->unsigned()->index();
			$table->string('finger_id', 191)->unique();
			$table->integer('department_id')->default(1)->index();
			$table->integer('designation_id')->default(1)->index();
			$table->integer('branch_id')->unsigned()->nullable()->default(1)->index();
			$table->boolean('incentive')->nullable()->default(0)->index();
			$table->boolean('work_shift')->nullable()->default(0)->index();
			$table->boolean('work_hours');
			$table->boolean('employee_category')->nullable();
			$table->integer('supervisor_id')->nullable()->index();
			$table->integer('hr_id')->nullable();
			$table->integer('operation_manager_id')->nullable();
			$table->integer('work_shift_id')->unsigned()->default(1)->index();
			$table->string('weekoff_updated_at', 50)->nullable()->index();
			$table->string('esi_card_number', 30)->nullable()->index();
			$table->string('pf_account_number', 30)->nullable()->index();
			$table->integer('pay_grade_id')->unsigned()->nullable()->default(0)->index();
			$table->integer('hourly_salaries_id')->unsigned()->nullable()->default(0)->index();
			$table->string('email', 50)->nullable()->unique();
			$table->string('first_name', 30)->index();
			$table->string('last_name', 30)->nullable()->index();
			$table->date('date_of_birth')->nullable()->index();
			$table->date('date_of_joining')->nullable()->index();
			$table->date('date_of_leaving')->nullable()->index();
			$table->string('gender', 10)->index();
			$table->string('religion', 50)->nullable()->index();
			$table->boolean('nationality')->nullable();
			$table->string('country')->nullable();
			$table->string('marital_status', 10)->nullable()->index();
			$table->string('photo', 250)->nullable()->index();
			$table->text('address')->nullable();
			$table->text('emergency_contacts')->nullable();
			$table->string('document_title', 191)->nullable()->index();
			$table->string('document_name', 191)->nullable()->index();
			$table->date('document_expiry')->nullable()->index();
			$table->string('document_title2', 191)->nullable()->index();
			$table->string('document_name2', 191)->nullable()->index();
			$table->date('document_expiry2')->nullable()->index();
			$table->string('document_title3', 191)->nullable()->index();
			$table->string('document_name3', 191)->nullable()->index();
			$table->date('document_expiry3')->nullable()->index();
			$table->string('document_title4', 191)->nullable()->index();
			$table->string('document_name4', 191)->nullable()->index();
			$table->date('document_expiry4')->nullable()->index();
			$table->string('document_title5', 191)->nullable()->index();
			$table->string('document_name5', 191)->nullable()->index();
			$table->date('document_expiry5')->nullable()->index();
			$table->text('document_title8')->nullable();
			$table->text('document_name8')->nullable();
			$table->date('expiry_date8')->nullable();
			$table->text('document_title9')->nullable();
			$table->text('document_name9')->nullable();
			$table->date('expiry_date9')->nullable();
			$table->text('document_title10')->nullable();
			$table->text('document_name10')->nullable();
			$table->date('expiry_date10')->nullable();
			$table->text('document_title11')->nullable();
			$table->text('document_name11')->nullable();
			$table->date('expiry_date11')->nullable();
			$table->text('document_title16')->nullable();
			$table->text('document_name16')->nullable();
			$table->date('expiry_date16')->nullable();
			$table->text('document_title17')->nullable();
			$table->text('document_name17')->nullable();
			$table->date('expiry_date17')->nullable();
			$table->text('document_title18')->nullable();
			$table->text('document_name18')->nullable();
			$table->date('expiry_date18')->nullable();
			$table->text('document_title19')->nullable();
			$table->text('document_name19')->nullable();
			$table->date('expiry_date19')->nullable();
			$table->text('document_title20')->nullable();
			$table->text('document_name20')->nullable();
			$table->date('expiry_date20')->nullable();
			$table->text('document_title21')->nullable();
			$table->text('document_name21')->nullable();
			$table->date('expiry_date21')->nullable();
			$table->string('phone', 191)->nullable()->index();
			$table->boolean('status')->default(1)->index();
			$table->boolean('salary_limit')->nullable()->default(0)->index()->comment('0-lessthen 20000,1-morethen 20000');
			$table->boolean('permanent_status')->default(0)->index();
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->string('device_employee_id', 191)->nullable()->index();
			$table->softDeletes();
			$table->timestamps();
			$table->float('annual_leave', 11)->nullable()->default(0.00);
			$table->float('basic_salary', 20, 3)->nullable();
			$table->decimal('increment', 10)->nullable();
			$table->decimal('housing_allowance', 10, 3)->nullable();
			$table->decimal('utility_allowance', 10, 3)->nullable();
			$table->decimal('transport_allowance', 10, 3)->nullable();
			$table->decimal('living_allowance', 10, 3)->nullable();
			$table->decimal('mobile_allowance', 10, 3)->nullable();
			$table->decimal('special_allowance', 10, 3)->nullable();
			$table->decimal('net_salary', 10, 3)->nullable();
			$table->string('account_number', 50)->nullable();
			$table->string('ifsc_number', 50)->nullable();
			$table->string('name_of_the_bank', 50)->nullable();
			$table->string('account_holder', 50)->nullable();
			$table->decimal('arrears', 10, 3)->nullable();
			$table->decimal('prem_others', 9, 3)->nullable()->default(0.000);
			$table->integer('ip_attendance')->nullable()->default(0);
			$table->decimal('education_and_club_allowance', 9, 3)->nullable();
			$table->decimal('membership_allowance', 9, 3)->nullable();
			$table->text('status_remark')->nullable();
			$table->integer('mobile_attendance')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('employee');
	}
}
