<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaidLeaveRulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('paid_leave_rules', function(Blueprint $table)
		{
			$table->increments('paid_leave_rule_id');
			$table->integer('branch_id')->nullable();
			$table->integer('for_year');
			$table->float('day_of_paid_leave');
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
		Schema::drop('paid_leave_rules');
	}

}
