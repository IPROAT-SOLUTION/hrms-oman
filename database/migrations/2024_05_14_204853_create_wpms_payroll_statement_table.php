<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWpmsPayrollStatementTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wpms_payroll_statement', function (Blueprint $table) {
			$table->integer('wpms_id', true);
			$table->text('employee_id_type');
			$table->text('employee_id');
			$table->text('reference_number');
			$table->text('employee_name');
			$table->string('employee_bic', 11);
			$table->text('employee_account');
			$table->string('salary_frequency', 1);
			$table->integer('number_of_working_days');
			$table->decimal('net_salary', 9, 3);
			$table->decimal('basic_salary', 9, 3);
			$table->decimal('extra_hours', 3);
			$table->decimal('extra_income', 9, 3);
			$table->decimal('deductions', 9, 3);
			$table->text('document_id');
			$table->decimal('social_security_deductions', 9, 3);
			$table->text('notes_comments');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wpms_payroll_statement');
	}
}
