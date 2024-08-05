<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalaryDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_details', function(Blueprint $table)
		{
			$table->increments('salary_details_id');
			$table->integer('employee_id');
			$table->integer('branch_id')->nullable();
			$table->string('month_of_salary', 20);
			$table->decimal('basic_salary', 9, 3)->default(0.000);
			$table->decimal('total_allowances', 9, 3)->default(0.000);
			$table->decimal('total_deductions', 9, 3)->default(0.000);
			$table->integer('total_late')->default(0);
			$table->decimal('total_late_amount', 9, 3)->default(0.000);
			$table->integer('total_absence')->default(0);
			$table->decimal('total_absence_amount', 9, 3)->default(0.000);
			$table->integer('overtime_rate')->default(0);
			$table->decimal('per_day_salary', 9, 3)->default(0.000);
			$table->string('total_over_time_hour', 50)->default('00:00');
			$table->decimal('total_overtime_amount', 9, 3)->default(0.000);
			$table->integer('hourly_rate')->default(0);
			$table->integer('total_present')->default(0);
			$table->integer('total_leave')->default(0);
			$table->decimal('public_holiday', 10)->default(0.00);
			$table->decimal('weekly_holiday', 10)->default(0.00);
			$table->integer('total_working_days')->default(0);
			$table->decimal('net_salary', 9, 3)->default(0.000);
			$table->integer('tax')->default(0);
			$table->integer('taxable_salary')->default(0);
			$table->string('working_hour', 191)->default('00:00');
			$table->decimal('gross_salary', 9, 3)->default(0.000);
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->boolean('status')->default(0);
			$table->text('comment')->nullable();
			$table->string('payment_method', 50)->nullable();
			$table->string('action', 50)->nullable();
			$table->timestamps();
			$table->decimal('increment', 9, 3)->default(0.000);
			$table->decimal('increment_amount', 10, 3)->default(0.000);
			$table->decimal('housing_allowance', 9, 3)->default(0.000);
			$table->decimal('utility_allowance', 9, 3)->default(0.000);
			$table->decimal('transport_allowance', 9, 3)->default(0.000);
			$table->decimal('living_allowance', 9, 3)->default(0.000);
			$table->decimal('mobile_allowance', 9, 3)->default(0.000);
			$table->decimal('special_allowance', 9, 3)->default(0.000);
			$table->decimal('social_security', 9, 3)->default(0.000);
			$table->text('account_number');
			$table->text('ifsc_number');
			$table->text('name_of_the_bank');
			$table->text('account_holder');
			$table->decimal('arrears_adjustment', 9, 3)->default(0.000);
			$table->decimal('pay_cut', 9, 3)->default(0.000);
			$table->decimal('gsm', 9, 3)->default(0.000);
			$table->decimal('prem_others', 9, 3)->default(0.000);
			$table->decimal('salary_advance', 9, 3)->default(0.000);
			$table->decimal('extra_hours', 3)->default(0.00);
			$table->decimal('extra_amount', 9, 3)->default(0.000);
			$table->decimal('membership_allowance', 9, 3)->default(0.000);
			$table->decimal('education_and_club_allowance', 9, 3)->default(0.000);
			$table->decimal('employer_contribution', 9, 3)->default(0.000);
			$table->decimal('lop', 9, 3)->default(0.000);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('salary_details');
	}

}
