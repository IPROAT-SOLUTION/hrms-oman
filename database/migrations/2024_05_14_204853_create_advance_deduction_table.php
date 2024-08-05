<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdvanceDeductionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('advance_deduction', function(Blueprint $table)
		{
			$table->increments('advance_deduction_id');
			$table->integer('employee_id');
			$table->decimal('advance_amount', 10, 3);
			$table->string('date_of_advance_given', 191);
			$table->decimal('deduction_amouth_per_month', 10, 3);
			$table->integer('no_of_month_to_be_deducted');
			$table->integer('remaining_month')->nullable();
			$table->string('advancededuction_name')->nullable();
			$table->boolean('status')->default(1)->comment('0-Active,1-Hold,2- No due');
			$table->timestamps();
			$table->integer('branch_id')->nullable();
			$table->decimal('paid_amount', 10, 3)->default(0.000);
			$table->decimal('pending_amount', 10, 3)->default(0.000);
			$table->boolean('payment_type')->default(0)->comment('0-Bank,1-Cash');
			$table->integer('created_by');
			$table->integer('updated_by');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('advance_deduction');
	}

}
