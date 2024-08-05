<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdvanceDeductionTransactionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('advance_deduction_transaction', function(Blueprint $table)
		{
			$table->integer('advance_deduction_transaction_id', true);
			$table->bigInteger('advance_deduction_log_id');
			$table->bigInteger('advance_deduction_id');
			$table->bigInteger('employee_id');
			$table->date('transaction_date');
			$table->integer('payment_type');
			$table->decimal('cash_received', 10, 3)->default(0.000);
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
			$table->timestamps();
			$table->decimal('pending_amount', 10, 3)->nullable();
			$table->integer('remaining_month')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('advance_deduction_transaction');
	}

}
