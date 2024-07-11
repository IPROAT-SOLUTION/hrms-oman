<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmpLeaveBalancesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('emp_leave_balances', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('employee_id');
			$table->bigInteger('department_id');
			$table->bigInteger('branch_id');
			$table->bigInteger('designation_id');
			$table->string('finger_id', 191);
			$table->bigInteger('leave_type_id');
			$table->decimal('leave_balance', 10)->default(0.00);
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
		Schema::drop('emp_leave_balances');
	}

}
